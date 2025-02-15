<?php
namespace AvailabilityPlus\Resolver\Driver;

class JournalsOnlinePrintElectronic extends JournalsOnlinePrint
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
    public function parseLinks($data_org) {
        $urls = []; // to check for duplicate urls
        $records = []; // array to return
        $freeAccess = false;
        $licensedAccess = false;
        $data = @simplexml_load_string($data_org, 'SimpleXMLElement', LIBXML_NOCDATA);
        $data = json_decode(json_encode($data), true);

        if (array_key_exists('@attributes', $data['Full']['ElectronicData']['ResultList']['Result'])) {
            $results = $data['Full']['ElectronicData']['ResultList'];
        } else {
            $results = $data['Full']['ElectronicData']['ResultList']['Result'];
        }

        foreach ($results as $result) {
            if (!empty($result['AccessURL'])) {
                $level = '';
                $label = '';
                $score = 0;
                $url = $result['AccessURL'];

                if (!in_array($url, $urls)) {
                    switch ($result['@attributes']['state']) {
                        case 0:
                            $freeAccess = true;
                            $level = 'FreeAccess link_external';
                            $label = 'FreeAccess';
                        case 2:
                            $licensedAccess = true;
                            if ($result['@attributes']['state'] != 0) {
                                $level = 'LicensedAccess link_external';
                                $label = 'LicensedAccess';
                                $score = $score + 10;
                            }
                            $level .= " {$result['AccessLevel']}";
                            $label .= "_{$result['AccessLevel']}";
                            if ($result['AccessLevel'] != 'article') $score = $score + 5;
                            $urls[] = $url;
                            break;
                    }
                    if (!empty($level)) {
                        $record['score'] = $score;
                        $record['level'] = $level;
                        $record['label'] = $label;
                        $record['url'] = $url;
                        $records[] = $record;
                    }
                }
            }
        }

        if (!empty($this->doi)) {
            if ($freeAccess) {
                $record['score'] = 0 - 1;
                $record['level'] = 'FreeAccess link_external';
                $record['label'] = 'FreeAccess';
                $record['url'] = "https://dx.doi.org/{$this->doi}";
                $records[] = $record;
            } else if ($licensedAccess) {
                $record['score'] = 10 - 1;
                $record['level'] = 'LicensedAccess link_external';
                $record['label'] = 'LicensedAccess';
                $record['url'] = "https://dx.doi.org/{$this->doi}";
                $records[] = $record;
            }
        }

        $response['data'] = $data_org;
        $this->parsed_data = $records;
        $this->applyCustomChanges();

        uasort($this->parsed_data, function($a, $b) {
            return $a['score'] <=> $b['score'];
        });

        $response['parsed_data'] = $this->parsed_data;

        return $response;
    }
}
