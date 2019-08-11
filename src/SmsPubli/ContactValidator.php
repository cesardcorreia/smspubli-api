<?php

namespace SmsPubli;

class ContactValidator
{
    public $is_valid = false, $data;
    private $country_code;

    /**
     * Checks via regex if content of string is digits.
     *
     * @param $contact
     * @return $this
     * @throws \Exception
     */
    private function validate_contact($contact)
    {
        $re = '/^([\d]*)$/m';
        preg_match_all($re, $contact, $matches, PREG_SET_ORDER, 0);

        if (count($matches) === 0 || empty($matches)) {
            throw new \Exception('This contact is invalid.');
        }

        return $this;
    }

    /**
     * Splits the first 0 to 3 characters/digits via regex to validate if is a suported
     * country code, in which assigns the data variable if it is or throws an error
     *
     * @param $contact
     * @return $this
     * @throws \Exception
     */
    private function get_country_code($contact)
    {
        $re = '/^([\d]{1,3})([\d]*)/m';
        preg_match_all($re, $contact, $matches, PREG_SET_ORDER, 0);

        //if nothing means it is an invalid country code
        if (count($matches) === 0 || empty($matches)) throw new \Exception('Error validating the country code');

        $country_codes = new SupportedCountryCodes();

        if ($info = $country_codes->country_codes[$matches[0][1]]) { // country code is supported
            $this->data = array_merge( $info,['contact' => $matches[0][2]]);
            return $this;
        }
        throw new \Exception('This country code is not supported yet');
    }

    private function validate_size()
    {
        $country_contact_size = $this->data['country_contact_size'];
        $contact = $this->data['contact'];
        if (strlen($contact) != $country_contact_size) {
            throw new \Exception('This contact size is not valid.');
        }

        return $this;
    }

    public function validate($contact)
    {
        $this->validate_contact($contact);
        $this->get_country_code($contact);
        $this->validate_size();

        $this->is_valid = true;

        return ['is_valid' => true, 'info' => $this->data];
    }

}