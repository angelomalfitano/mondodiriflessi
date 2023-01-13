<?php

class SLN_Shortcode_Salon_AttendantStep extends SLN_Shortcode_Salon_Step
{

    protected function dispatchForm()
    {

        if(isset($_POST['sln'])){
            $attendants                 = isset($_POST['sln']['attendants']) ? array_map('intval',$_POST['sln']['attendants']) : array();
            $attendant                 = isset($_POST['sln']['attendant']) ? sanitize_text_field(wp_unslash($_POST['sln']['attendant'])) : false;
        }
        $isMultipleAttSelection = $this->getPlugin()->getSettings()->isMultipleAttendantsEnabled();
        $bb                     = $this->getPlugin()->getBookingBuilder();
        $ah                     = $this->getPlugin()->getAvailabilityHelper();
        $ah->setDate($bb->getDateTime());
        $bb->removeAttendants();

        if(empty($attendant) && empty($attendants) && $this->getPlugin()->getSettings()->isFormStepsAltOrder() && !(isset($_POST['attendant_auto']) && $_POST['attendant_auto'] === true)){ return true; }

        $bservices = $bb->getAttendantsIds();
        $date      = $bb->getDateTime();

        if ($isMultipleAttSelection) {
            $ids = isset($attendants) ? $attendants : array();

            $ret = $this->dispatchMultiple($bservices, $date, $ids);
        } else {
            $id = isset($attendant) ? $attendant : null;

            $ret = $this->dispatchSingle($bservices, $date, $id);
        }

        if (is_array($ret)) {
            $bb->setServicesAndAttendants($ret);
        }

        if ($ret) {
            $bb->save();

            return true;
        } else {
            return false;
        }
    }

    public function dispatchMultiple($services, $date, $selected)
    {
        $bb = $this->getPlugin()->getBookingBuilder();
        $ah = $this->getPlugin()->getAvailabilityHelper();
        $ah->setDate($date);
        $bookingServices = SLN_Wrapper_Booking_Services::build($services, $date, 0, $bb->getCountServices());

        $availAtts               = null;
        $availAttsForEachService = array();

        foreach ($bookingServices->getItems() as $bookingService) {
            $service = $bookingService->getService();
            if (!$service->isAttendantsEnabled()) {
                continue;
            }
            $tmp                                        = $ah->getAvailableAttsIdsForBookingService($bookingService);
            $availAttsForEachService[$service->getId()] = $tmp;
            if (empty($tmp)) {
                $this->addError(
                    esc_html(sprintf(
                        __('No one of the attendants isn\'t available for %s service', 'salon-booking-system'),
                        $service->getName()
                    ))
                );

                return false;
            } elseif (!empty($selected[$service->getId()])) {
                $attendantId  = $selected[$service->getId()];
                $hasAttendant = in_array($attendantId, $availAttsForEachService[$service->getId()]);
                if (!$hasAttendant) {
                    $attendant = $this->getPlugin()->createAttendant($attendantId);
                    $this->addError(
                        sprintf(
                            esc_html__('Attendant %s isn\'t available for %s service at %s', 'salon-booking-system'),
                            $attendant->getName(),
                            $service->getName(),
                            $ah->getDayBookings()->getTime(
                                $bookingService->getStartsAt()->format('H'),
                                $bookingService->getStartsAt()->format('i')
                            )
                        )
                    );

                    return false;
                }
            }

        }

        $ret = array();
        $unavAttForParallelServices = array();

        foreach ($bookingServices->getItems() as $bookingService) {
            $service = $bookingService->getService();

            if (!$service->isAttendantsEnabled()) {
                $ret[$service->getId()] = 0;
                continue;
            }

            if (!empty($selected[$service->getId()])) {
                $attId = $selected[$service->getId()];
            } else {
                $availAtts = $availAttsForEachService[$service->getId()];
                if ($service->isExecutionParalleled()) {
                    $availAtts = array_values(array_diff($availAtts, $unavAttForParallelServices));
                }
                $index = mt_rand(0, count($availAtts) - 1);
                $attId = $availAtts[$index];
            }

            if (!$attId) {
                $this->addError(
                    sprintf(
                        esc_html__('There is no attendants available for %s service at %s', 'salon-booking-system'),
                        $service->getName(),
                        $ah->getDayBookings()->getTime(
                            $bookingService->getStartsAt()->format('H'),
                            $bookingService->getStartsAt()->format('i')
                        )
                    )
                );

                return false;
            }

            $ret[$service->getId()] = $attId;

            if ($service->isExecutionParalleled()) {
                $unavAttForParallelServices[] = $attId;
            }
        }

        return $ret;
    }

    public function dispatchSingle($services, $date, $selected)
    {
        $bb = $this->getPlugin()->getBookingBuilder();
        $ah = $this->getPlugin()->getAvailabilityHelper();
        $ah->setDate($date);
        $bookingServices = SLN_Wrapper_Booking_Services::build($services, $date, 0, $bb->getCountServices());

        $availAtts = null;
        foreach ($bookingServices->getItems() as $bookingService) {
            if (!$bookingService->getService()->isAttendantsEnabled()) {
                continue;
            }
            $availAtts = $ah->getAvailableAttendantForService($availAtts, $bookingService);

            if (empty($availAtts)) {
                $this->addError(
                    __('No one of the attendants isn\'t available for selected services', 'salon-booking-system')
                );

                return false;
            }
        }

        if (!$selected) {
            if (count($availAtts)) {
                $index = mt_rand(0, count($availAtts) - 1);
                $attId = $availAtts[$index];
            } else {
                $attId = 0;
            }
        } else {
            $attId = $selected;
        }

        $ret = array();
        foreach ($bookingServices->getItems() as $bookingService) {
            $service = $bookingService->getService();

            if (!$service->isAttendantsEnabled()) {
                $ret[$service->getId()] = 0;
                continue;
            }

            $ret[$service->getId()] = $attId;
        }

        return $ret;
    }


    /**
     * @return SLN_Wrapper_Attendant[]
     */
    public function getAttendants()
    {
        if (!isset($this->attendants)) {
            /** @var SLN_Repository_AttendantRepository $repo */
            $repo             = $this->getPlugin()->getRepository(SLN_Plugin::POST_TYPE_ATTENDANT);
            $this->attendants = $repo->sortByPos($repo->getAll());
            $this->attendants = apply_filters('sln.shortcode.salon.AttendantStep.getAttendants', $this->attendants);
        }

        return $this->attendants;
    }

    public function isValid()
    {
        $tmp = $this->getAttendants();
        $bb = $this->getPlugin()->getBookingBuilder();
        if($this->getPlugin()->getSettings()->get('skip_attendants_enabled')){
            return !empty($tmp) && !parent::isValid() && $this->isSkipAttendants($tmp);
        }

        return (!empty($tmp)) && parent::isValid();
    }

    protected function isSkipAttendants($attendants){
        $bb = $this->getPlugin()->getBookingBuilder();
        $ah = $this->getPlugin()->getAvailabilityHelper();
        $ah->setDate($this->getPlugin()->getBookingBuilder()->getDateTime());
        $bookingServices = SLN_Wrapper_Booking_Services::build($bb->getAttendantsIds(), $bb->getDateTime(), 0, $bb->getCountServices());
        $validAttendants = array();
        if(!$this->getPlugin()->getSettings()->isMultipleAttendantsEnabled()){
            $services = $bb->getServices();
            foreach($attendants as $attendant){
                if(
                    empty(SLN_Shortcode_Salon_AttendantHelper::validateItem($bookingServices->getItems(), $ah, $attendant)) &&
                    $attendant->hasServices($services)
                ){
                    if(!empty($validAttendants)){
                        return false;
                    }
                    $validAttendants = $attendant;
                }
            }
            if(empty($validAttendants)){
                return false;
            }
            $ret = $this->dispatchSingle($bb->getAttendantsIds(), $bb->getDateTime(), $validAttendants->getId());
            if (is_array($ret)) {
                $bb->setServicesAndAttendants($ret);
                $bb->save();
            }
        }else{
            foreach($bookingServices as $bookingService){
                foreach($attendants as $attendant){
                    if(
                        SLN_Shortcode_Salon_AttendantHelper::validateItem($bookingServices->getItems(), $ah, $attendant) &&
                        $attendant->hasService($bookingService->getService())
                    ){
                        if(isset($validAttendants[$bookingService->getService()->getId()])){
                            return false;
                        }
                        $validAttendants[$bookingService->getService()->getId()] = $attendant->getId();
                    }
                }
                if(!isset($validAttendants[$bookingService->getService()->getId()])){
                    return false;
                }
            }
            $ret = $this->dispatchMultiple($bb->getAttendantsIds(), $bb->getDateTime(), $validAttendants);
            if (is_array($ret)) {
                $bb->setServicesAndAttendants($ret);
                $bb->save();
            }
        }
        return true;
    }
}
