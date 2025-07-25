<?php
namespace AvailabilityPlus\Resolver\Driver;

class SFX extends AvailabilityPlusResolver
{
    /**
     * Parse Links
     *
     * Parses an XML file returned by a link resolver
     * and converts it to a standardised format for display
     *
     * @param string $xmlstr Raw XML returned by resolver
     *
     * @return array         Array of values
     */
    public function parseLinks($data_org): array {
        $records = []; // array to return
        $results = [];
        $data = @simplexml_load_string($data_org, 'SimpleXMLElement', LIBXML_NOCDATA);
        $data = json_decode(json_encode($data), true);

        if (!empty($data['ctx_obj']['ctx_obj_targets']['target'])) {
            if (!isset($data['ctx_obj']['ctx_obj_targets']['target']['target_name'])) {
                $results = $data['ctx_obj']['ctx_obj_targets']['target'];
            } else {
                $results = $data['ctx_obj']['ctx_obj_targets'];
            }
        }

        foreach ($results as $result) {
            $record['score'] = 0;
            $record['level'] = 'FreeAccess link_external';
            $record['label'] = "FreeAccess_{$result['service_type']}";
            $record['url'] = $result['target_url'] ?? '';

            if (!empty($coverage = $result['coverage'])) {
                if (!empty($coverage['from'])) {
                    $record['additionals']['from'] = $coverage['from'];
                }

                if (!empty($coverage['to'])) {
                    $record['additionals']['to'] = $coverage['to'];
                }
            }

            if (!empty($record['url'])) {
                $records[] = $record;
            }
        }

        $response['data'] = $data_org;
        $this->parsed_data = $records;
        $this->applyCustomChanges();

        $response['parsed_data'] = $this->parsed_data;

        return $response;
    }
}
