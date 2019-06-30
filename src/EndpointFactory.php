<?php


namespace Coas\TCU;


interface EndpointFactory
{

    /**
     * Check the status of an applicant (eligibility for application)
     *
     * @param $indexNumber
     * @return mixed
     */
    public function checkStatus($indexNumber);

    /**
     * Upload applicants’ f4indexno and f6indexno
     *
     * @param $category
     * @param $indexF4
     * @param $indexF6
     * @param null $formFour
     * @param null $formSix
     * @return mixed
     */
    public function add($category, $indexF4, $indexF6, $formFour = null, $formSix = null);

    /**
     * PUSH applicants with their selected programmes, programme admitted to and contact details
     *
     * @param $indexF4
     * @param $indexF6
     * @param $programmes
     * @param $admitted
     * @param $status
     * @param $dob
     * @param null $phone
     * @param null $email
     * @param null $reason
     * @return mixed
     */
    public function submitProgramme($indexF4, $indexF6, $programmes,$admitted,$status,$dob,$phone = null, $email = null, $reason = null);

    /**
     * PUSH to TCU special confirmation code entered by applicants with multiple admissions
     *
     * @param $indexF4
     * @param $code
     * @return mixed
     */
    public function confirm($indexF4, $code);

    /**
     * PUSH to TCU applicants who chose to reject their admission
     *
     * @param $indexF4
     * @param $code
     * @param string $action
     * @return mixed
     */
    public function reject($indexF4, $code, $action = 'REJECT');

    /**
     * PUSH applicants whose details have changed since last submission
     *
     * @param $indexF4
     * @param $indexF6
     * @param $programmes
     * @param $accepted_programme
     * @param $status
     * @param $dob
     * @param null $phone
     * @param null $email
     * @param null $reason
     * @param null $otherformFour
     * @param null $otherformSix
     * @return mixed
     */
    public function resubmit($indexF4, $indexF6, $programmes, $accepted_programme, $status ,$dob, $phone = null, $email = null, $reason = null, $otherformFour = null, $otherformSix = null);

    /**
     * Fetch a list of all applicants in a given programme
     *
     * @param $programme
     * @return mixed
     */
    public function getStatus($programme);

    /**
     * PULL a list of confirmed applicants (from those with multiple admissions)
     *
     * @param $programme
     * @return mixed
     */
    public function getConfirmed($programme);

    /**
     * Download the admission status of all applicants in a programme
     *
     * @param $programme
     * @return mixed
     */
    public function getAdmitted($programme);

    /**
     * Get programmes with Admitted candidates
     *
     * @return mixed
     */
    public function getProgrammes();

    /**
     * Upload summary of applications for statistical purposes
     *
     * @param $programme
     * @param $males
     * @param $females
     * @return mixed
     */
    public function populate($programme, $males, $females);

}