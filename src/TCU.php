<?php

namespace Coas\TCU;

use Coas\TCU\Exception\ErrorTCUHandlerException;
use GuzzleHttp\Client;
use SimpleXMLElement;


/**
 *
 * Wrapper for TCU API
 *
 * @version    1.0.0
 * @package    coas/tcu
 * @copyright  Copyright (c) 2018 - 2019 Boraone (http://coas.co.tz)
 * @author     Amani Mawalla <amawalla@boraone.com>
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */

class TCU implements EndpointFactory
{

    protected $username;

    protected $token;

    protected $institutionCode;

    protected $inJson = true;

    const ADMISSION_URI = 'http://197.149.178.22/admission/';

    const APPLICANT_URI = 'http://api.tcu.go.tz/applicants/';

    const DASHBOARD_URI = 'http://197.149.178.22/dashboard/';

    protected $requestParameters;

    protected $baseurl = self::APPLICANT_URI;

    public $responseBody;

    public $generatedXMLBody;

    protected $admissionURL;

    protected $applicantURL;

    protected $dashboardURL;


    /**
     * TCU constructor.
     * @param $username
     * @param $token
     * @param $institution_code
     * @param bool $json
     */
    public function __construct($username, $token, $institution_code = null, $json = true)
    {
        $this->username = $username;

        $this->inJson = (bool)$json;

        $this->token = $token;

        $this->institutionCode = !empty($institution_code) ? $institution_code : $this->username;

        $this->admissionURL = self::ADMISSION_URI;

        $this->applicantURL = self::APPLICANT_URI;

        $this->dashboardURL = self::DASHBOARD_URI;
    }

    /**
     * Sets the username
     *
     * @param $string
     * @return TCU
     */
    public function setUsername($string)
    {
        $this->username = $string;
        return $this;
    }


    /**
     *  Sets the session token
     *
     * @param $string
     * @return TCU
     */
    public function setSessionToken($string)
    {

        $this->token = $string;
        return $this;
    }

    /**
     *  Sets the institution code
     *
     * @param $string
     * @return TCU
     */
    public function setInstitutionCode($string)
    {
        $this->institutionCode = $string;
        return $this;
    }

    /**
     * @param $string
     * @return TCU
     * @throws ErrorTCUHandlerException
     */
    public function setBaseUrl($string)
    {
        if(filter_var($string, FILTER_VALIDATE_URL) == false) {

            throw new ErrorTCUHandlerException('Invalid Base URL provided');
        }

        $this->baseurl = $string;


        return $this;
    }

    /**
     * @param $string
     * @return $this
     * @throws ErrorTCUHandlerException
     */
    public function setDashboardURL($string) {

        if(filter_var($string, FILTER_VALIDATE_URL) == false) {

            throw new ErrorTCUHandlerException('Invalid URL provided');
        }

        $this->dashboardURL = $string;

        return $this;

    }

    /**
     * @param $string
     * @return $this
     * @throws ErrorTCUHandlerException
     */
    public function setAdmissionURL($string) {

        if(filter_var($string, FILTER_VALIDATE_URL) == false) {

            throw new ErrorTCUHandlerException('Invalid URL provided');
        }

        $this->admissionURL = $string;

        return $this;

    }

    /**
     * @param $string
     * @return $this
     * @throws ErrorTCUHandlerException
     */
    public function setApplicantURL($string) {

        if(filter_var($string, FILTER_VALIDATE_URL) == false) {

            throw new ErrorTCUHandlerException('Invalid URL provided');
        }

        $this->applicantURL = $string;

        return $this;
    }


    /**
     * @param bool $json
     * @return TCU
     */
    public function inJson($json = true)
    {

        $this->inJson = $json;

        return $this;
    }

    /**
     * Check the status of an applicant (eligibility for application)
     *
     * @param $indexNumber
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function checkStatus($indexNumber)
    {
        if (is_string($indexNumber)) {

            $this->requestParameters = ['RequestParameters' => ['f4indexno' => $indexNumber]];

        } elseif (is_array($indexNumber)) {

            foreach ($indexNumber as $item) {

                $data[] = ['f4indexno' => $item];
            }
            $this->requestParameters = ['RequestParameters' => $data];
        }

        return $this->sendRequest(__FUNCTION__);
    }

    /**
     * Upload applicants’ f4indexno and f6indexno
     *
     * @param $category
     * @param $indexF4
     * @param $indexF6
     * @param null $formFour
     * @param null $formSix
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function add($category, $indexF4, $indexF6, $formFour = null, $formSix = null)
    {

        $this->requestParameters = [
            'Category' => $category,
            'f4indexno' => $indexF4,
            'f6indexno' => $indexF6,
            'Otherf4indexno' => is_array($formFour) ? implode(",", $formFour) : $formFour,
            'Otherf6indexno' => is_array($formSix) ? implode(",", $formSix) : $formSix,
        ];

        return $this->sendRequest(__FUNCTION__);
    }


    /**
     * @param $params
     * @return SimpleXMLElement|string
     * @throws ErrorTCUHandlerException
     */
    public function addBatch($params = [])
    {
        if (!is_array($params)) {

            throw new ErrorTCUHandlerException('Param must be of type array');
        }

        foreach ($params as $item) {
            $data[] = $item;
        }

        $this->requestParameters = $data;

        return $this->sendRequest('add');
    }

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
     * @param null $otherPhone
     * @param null $nationality
     * @param null $disability
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function submitProgramme($indexF4, $indexF6, $programmes, $admitted, $status, $dob, $phone = null, $email = null, $reason = null,$nationality = null, $disability = null, $otherPhone = null)
    {
        $this->requestParameters = [
            'f4indexno' => $indexF4,
            'f6indexno' => $indexF6,
            'SelectedProgrammes' => $programmes,
            'MobileNumber' => $phone,
            'OtherMobileNumber' => $otherPhone ?? '',
            'EmailAddress' => $email,
            'AdmissionStatus' => $status,
            'ProgrammeAdmitted' => $admitted,
            'Reason' => $reason,
            'Nationality' => $nationality ?? 'Tanzanian',
            'Impairment' => $disability ?? 'None',
            'DateOfBirth' => $dob
        ];


        return $this->sendRequest(__FUNCTION__);
    }

    /**
     * PUSH to TCU special confirmation code entered by applicants with multiple admissions
     *
     * @param $indexF4
     * @param $code
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function confirm($indexF4, $code)
    {
        $this->requestParameters = [
            'f4indexno' => $indexF4,
            'ConfirmationCode' => $code,
        ];

        return $this->sendRequest(__FUNCTION__);
    }

    /**
     * PUSH to TCU applicants who chose to reject their admission
     *
     * @param $indexF4
     * @param $code
     * @param string $action
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function reject($indexF4, $code, $action = 'REJECT')
    {
        $this->requestParameters = [
            'f4indexno' => $indexF4,
            'ConfirmationCode' => $code,
        ];

        return $this->sendRequest(__FUNCTION__);
    }


    /**
     * PUSH to TCU applicants who chose to reject their admission
     *
     * @param $indexF4
     * @param $code
     * @param string $action
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function unconfirm($indexF4, $code)
    {
        $this->requestParameters = [
            'institutionCode' => $this->institutionCode,
            'f4indexno' => $indexF4,
            'ConfirmationCode' => $code,
        ];

        return $this->sendRequest(__FUNCTION__);
    }

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
     * @param null $nationality
     * @param null $disability
     * @param null $otherformFour
     * @param null $otherformSix
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function resubmit($indexF4, $indexF6, $programmes, $accepted_programme, $status, $dob ,$phone = null, $email = null, $reason = null,$nationality= null,$disability = null, $otherformFour = null, $otherformSix = null)
    {
        $this->requestParameters = [
            'f4indexno' => $indexF4,
            'f6indexno' => $indexF6,
            'SelectedProgrammes' => $programmes,
            'MobileNumber' => $phone,
            'EmailAddress' => $email,
            'AdmissionStatus' => $status,
            'ProgrammeAdmitted' => $accepted_programme,
            'Category' => 'Eligible',
            'Reason' => $reason,
            'Nationality' => $nationality ?? 'Tanzanian',
            'Impairment' => $disability ?? 'None',
            'DateOfBirth' => $dob ?? null,
            'Other_f4indexno' => is_array($otherformFour) ? implode(",", $otherformFour) : $otherformFour,
            'Other_f6indexno' => is_array($otherformSix) ? implode(",", $otherformSix) : $otherformSix,
        ];

        /*
         *  Fix for otherFormFour & otherFormSix when there is not data the API wasn't successfully
         */
        $this->requestParameters = array_filter($this->requestParameters, function ($i) {
            return $i != '';
        });

        return $this->sendRequest(__FUNCTION__);
    }

    /**
     * @param $indexF4
     * @param $indexF6
     * @param $current_programme
     * @param $previous_programme
     * @return SimpleXMLElement|string
     * @throws ErrorTCUHandlerException
     */
    public function submitInterInstitutionalTransfers($indexF4, $indexF6, $current_programme, $previous_programme) {

        $this->requestParameters = [
            'f4indexno' => $indexF4,
            'f6indexno' => $indexF6,
            'CurrentProgrammeCode' => $current_programme,
            'PreviousProgrammeCode' => $previous_programme,
        ];

        return $this->setBaseUrl($this->admissionURL)
            ->sendRequest(__FUNCTION__);
    }


    /**
     * @param $indexF4
     * @param $indexF6
     * @param $current_programme
     * @param $previous_programme
     * @return SimpleXMLElement|string
     * @throws ErrorTCUHandlerException
     */
    public function submitInternalTransfers($indexF4, $indexF6, $current_programme, $previous_programme) {

        $this->requestParameters = [
            'f4indexno' => $indexF4,
            'f6indexno' => $indexF6,
            'CurrentProgrammeCode' => $current_programme,
            'PreviousProgrammeCode' => $previous_programme,
        ];

        return $this->setBaseUrl($this->admissionURL)
            ->sendRequest(__FUNCTION__);
    }

    /**
     * @param $programme
     * @return SimpleXMLElement|string
     * @throws ErrorTCUHandlerException
     */
    public function getApplicantVerificationStatus($programme) {

        $this->requestParameters = [
            'ProgrammeCode' => $programme,
        ];

        return $this->sendRequest(__FUNCTION__);
    }

    /**
     * @param $indexNumber
     * @param $phone
     * @return SimpleXMLElement|string
     * @throws ErrorTCUHandlerException
     */
    public function requestConfirmationCode($indexNumber, $phone) {

        $this->requestParameters = [
            'f4Index' => $indexNumber,
            'MobileNumber' => $phone,
        ];

        return $this->setBaseUrl($this->admissionURL)
            ->sendRequest(__FUNCTION__);
    }

    /**
     * Fetch a list of all applicants in a given programme
     *
     * @param $programme
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function getStatus($programme)
    {
        $this->requestParameters = [
            'Programme' => $programme,
        ];

        return $this->sendRequest(__FUNCTION__);
    }

    /**
     * PULL a list of confirmed applicants (from those with multiple admissions)
     *
     * @param $programme
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function getConfirmed($programme)
    {
        $this->requestParameters = [
            'Programme' => $programme,
        ];

        return $this->sendRequest(__FUNCTION__);
    }

    /**
     * Download the admission status of all applicants in a programme
     *
     * @param $programme
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function getAdmitted($programme)
    {
        $this->requestParameters = [
            'Programme' => $programme,
        ];

        return $this
            ->setBaseUrl($this->admissionURL)
            ->sendRequest(__FUNCTION__);
    }

    /**
     * Get programmes with Admitted candidates
     *
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function getProgrammes()
    {
        return $this->sendRequest(__FUNCTION__);
    }

    /**
     * Upload summary of applications for statistical purposes
     *
     * @param $programme
     * @param $males
     * @param $females
     * @return mixed
     * @throws ErrorTCUHandlerException
     */
    public function populate($programme, $males, $females)
    {
        $this->requestParameters = [
            'Programme' => $programme,
            'Males' => $males,
            'Females' => $females
        ];

        return $this->setBaseUrl($this->dashboardURL)->sendRequest(__FUNCTION__);
    }


    /**
     *  Generate XML request body
     *
     * @param array $data
     * @param string $node
     * @param bool $multiple
     * @return string
     */
    protected function generateRequestBody($data = [], $node = 'RequestParameters')
    {
        $data = $data ? $data : $this->requestParameters;

        $request = [$node => $data];

        if ($this->is_multidimensional($data)) {
            $request = null;
            foreach ($data as $key => $item) {
                $request[] = [$node => $item];
            }
        }

        return ArrayToXml::convert(array_merge_recursive([
            'UsernameToken' =>
                [
                    'Username' => $this->username,
                    'SessionToken' => $this->token,
                ]
        ], $request), 'Request');
    }


    /**
     * Checks to see if the array is multi-dimensional
     *
     *
     * @param array $array
     * @return bool
     */
    public function is_multidimensional(array $array)
    {
        return count($array) !== count($array, COUNT_RECURSIVE);
    }


    /**
     *
     * Sends the request to TCU
     *
     * @param null $url
     * @param string $method
     * @param bool $json
     * @return SimpleXMLElement|string
     * @throws ErrorTCUHandlerException
     */
    public function sendRequest($url = null, $method = 'POST', $json = true)
    {
        if (empty($this->requestParameters)) {

            throw new ErrorTCUHandlerException('Missing Request Parameters');
        }

        if ($method && !in_array($method, ['GET', 'POST', 'get', 'post'])) {

            throw new ErrorTCUHandlerException('Invalid/Unknown "' . $method . '" HTTP method defined');

        } elseif (empty($method)) {

            $method = 'POST';
        }

        $method = strtolower($method);

        $url = !empty($url) ? $url : $url;

        $client = new Client(['base_uri' => $this->baseurl]);

        $response = $client->$method($url, [
            'headers' => [
                'Accept' => 'application/xml',
            ],
            'body' => $this->generatedXMLBody = $this->generateRequestBody()
        ]);

        $this->inJson($json);

        //$this->responseBody = $response;

        if ($response->getStatusCode() == 200 && $this->inJson) {

            return $this->parseResponse($response->getBody()->getContents());
        }

        return $response ? $response->getBody()->getContents() : null;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {

        return $this->responseBody;
    }

    /**
     * @param $string
     */
    public function setResponseBody($string)
    {
        $this->responseBody = $string;
    }


    /**
     * @param $content
     * @return SimpleXMLElement|string
     */
    protected function parseResponse($content)
    {
        return simplexml_load_string($content);
    }
}