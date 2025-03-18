<?php

namespace Creare\Tripleseat;
use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

class Tripleseat extends \Breakdance\Forms\Actions\Action {

    /**
     * The name of the action
     * @return string
     */
    public static function name() {
        return 'Tripleseat';
    }

    /**
     * @return string
    */
    public static function slug()
    {
        return 'tripleseat_feed';
    }


    public function snatch_form_fields() {

    }



    /**
    * Post the form data to Tripleseat
    *
    * @param array $form
    * @param array $settings
    * @param array $extra
    * @return array success or error message
    */
    public function run($form, $settings, $extra)
    {


        $has_action = false;
        $form_data = [];
        $return_message = '';

        try {

            //Find our action
            foreach( $settings['actions']['actions'] as $action_slug ) {
                if ( $action_slug == self::slug() ) {

                    //We've found our action
                    $has_action = true;

                }
            }

            if ( !$has_action ) {
                return ['type' => 'error', 'message' => 'Action not found'];
            }
            

            //Get the public key
            $public_key = $settings['actions'][self::slug()]['public_key'];

            if ( empty( $public_key ) ) {
                return ['type' => 'user-error', 'message' => 'Public key not set'];
            }

            $lead_first_name = $this->renderData( $form, $settings['actions'][self::slug()]['first_name'] );
            $lead_last_name = $this->renderData( $form, $settings['actions'][self::slug()]['last_name'] );
            $lead_email_address = $this->renderData( $form, $settings['actions'][self::slug()]['email_address'] );
            $lead_phone_number = $this->renderData( $form, $settings['actions'][self::slug()]['phone_number'] );

            if ( empty( $lead_first_name ) ) {
                return ['type' => 'user-error', 'message' => 'Required mapped value for First Name is empty'];
            }
            
            if ( empty( $lead_last_name ) ) {
                return ['type' => 'user-error', 'message' => 'Required mapped value for Last Name is empty'];
            }

            if ( empty( $lead_email_address ) ) {
                return ['type' => 'user-error', 'message' => 'Required mapped value for Email Address is empty'];
            }

            if ( empty( $lead_phone_number ) ) {
                return ['type' => 'user-error', 'message' => 'Required mapped value for Phone Number is empty'];
            }

            $form_data['first_name'] = !empty( $lead_first_name ) ? $lead_first_name : null;
            $form_data['last_name'] = !empty( $lead_last_name ) ? $lead_last_name : null;
            $form_data['email_address'] = !empty( $lead_email_address ) ? $lead_email_address : null;
            $form_data['phone_number'] = !empty( $lead_phone_number ) ? $lead_phone_number : null;

            foreach( $settings['actions'][self::slug()]['fields_map'] as $field_map ) {

                $tripleseat_field = $field_map['tripleseat_field'];
                $form_field = $field_map['form_field'];

                $form_data[$tripleseat_field] = $this->renderData( $form, $form_field );

            }

            //Now we'll put the form data into the Tripleseat API
            $url = "https://api.tripleseat.com/v1/leads/create.js?public_key={$public_key}"; 


            $lead = [];
            foreach( $form_data as $key => $value ) {

                $lead["lead[{$key}]"] = $value;

            }

            error_log( 'Lead data: ' . json_encode( $lead ) );

            $response = wp_remote_post( $url, [
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => [],
                'body' => $lead,
                'cookies' => []
            ]);

            $tripleseat_response = json_decode( $response['body'] );

            error_log( print_r( $tripleseat_response, 1 ) );

            
            if ( is_wp_error( $response ) ) {
                error_log( 'This is a WP Error:'. $response->get_error_message() );
                throw new \Exception( $response->get_error_message() );
//                return ['type' => 'error', 'message' => $response->get_error_message()];
            } elseif ( isset( $tripleseat_response->errors ) ) {
               
                error_log('Tripleseat error: ' );
            
                foreach( $tripleseat_response->errors as $key => $error ) {
                    $msg = $key . ': ' . implode( ',', $error );
                    error_log( $msg );
                    $error_message .= $msg . ' ';
                }
                
                //throw new \Exception( json_encode( $tripleseat_response->errors ) );
                return ['type' => 'user-error', 'message' => $error_message ];

            } else {
                error_log( 'Tripleseat response:' . $response['body'] );
                $return_message = $response['body'];
                $tripleseat_response = json_decode( $response['body'] );
            }

        } catch(Exception $e) {

            error_log( 'Logging the thrown exception message: ' . $e->getMessage() );

            return ['type' => 'user-error', 'message' => $e->getMessage()];

        }

        return ['type' => 'success', 'message' => $return_message ];
    
    }




    /**
     * Controls
     */
    public function controls() {
        return [c(
            "public_key",
            "Public Key",
            [],
            ['type' => 'text', 'layout' => 'vertical'],
            false,
            false,
            [],
          ), c(
            "first_name",
            "First Name",
            [],
            ['type' => 'text', 'layout' => 'vertical', 'variableOptions' => ['enabled' => true, 'populate' => ['path' => 'content.form.fields', 'text' => 'label', 'value' => 'advanced.id']]],
            false,
            false,
            [],
          ), c(
            "last_name",
            "Last Name",
            [],
            ['type' => 'text', 'layout' => 'vertical', 'variableOptions' => ['enabled' => true, 'populate' => ['path' => 'content.form.fields', 'text' => 'label', 'value' => 'advanced.id']]],
            false,
            false,
            [],
          ), c(
            "email_address",
            "Email Address",
            [],
            ['type' => 'text', 'layout' => 'vertical', 'variableOptions' => ['enabled' => true, 'populate' => ['path' => 'content.form.fields', 'text' => 'label', 'value' => 'advanced.id']]],
            false,
            false,
            [],
          ), c(
            "phone_number",
            "Phone Number",
            [],
            ['type' => 'text', 'layout' => 'vertical', 'variableOptions' => ['enabled' => true, 'populate' => ['path' => 'content.form.fields', 'text' => 'label', 'value' => 'advanced.id']]],
            false,
            false,
            [],
          ), c(
            "fields_map",
            "Fields Map",
            [c(
            "tripleseat_field",
            "Tripleseat Field",
            [],
            ['type' => 'dropdown', 'layout' => 'vertical', 'items' => [['text' => 'Contact Preference', 'value' => 'contact_preference'], ['text' => 'Company', 'value' => 'company'], ['text' => 'Nature of Event', 'value' => 'event_description'], ['text' => 'Location ID', 'value' => 'location_id'], ['text' => 'Event Date', 'value' => 'event_date'], ['text' => 'Start Time', 'value' => 'start_time'], ['text' => 'End Time', 'value' => 'end_time'], ['text' => 'Guest Count', 'value' => 'guest_count'], ['text' => 'Additional Information', 'value' => 'addition_information'], ['text' => 'Lead Form ID', 'value' => 'lead_form_id'], ['text' => 'Email Opt-in', 'value' => 'email_opt_in'], ['text' => 'Lead Source ID', 'value' => 'lead_source_id'], ['text' => 'Referral Source ID', 'value' => 'referral_source_id'], ['text' => 'Referral Source (Other)', 'value' => 'referral_source_other'], ['text' => 'GFPR Consent (value should be 1 if consented)', 'value' => 'gdpr_consent_granted'], ['text' => 'Event Style', 'value' => 'event_style'], ['value' => 'campaign_source', 'text' => 'Campaign Source'], ['text' => 'Campaign Medium', 'value' => 'campaign_medium'], ['value' => 'campaign_name', 'text' => 'Campaign Name'], ['value' => 'campaign_term', 'text' => 'Campaign Term'], ['value' => 'campaign_content', 'text' => 'Campaign Content']]],
            false,
            false,
            [],
          ), c(
            "form_field",
            "Form Field",
            [],
            ['type' => 'text', 'layout' => 'vertical', 'variableOptions' => ['enabled' => true, 'populate' => ['path' => 'content.form.fields', 'text' => 'label', 'value' => 'advanced.id']]],
            false,
            false,
            [],
          )],
            ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{tripleseat_field}', 'defaultTitle' => 'Field', 'buttonName' => 'Add Field']],
            false,
            false,
            [],
          )];
    }
}
