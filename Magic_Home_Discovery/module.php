<?php

declare(strict_types=1);

// Generell funktions
require_once __DIR__ . '/../libs/_traits.php';

// CLASS MagicHomeDiscovery
class MagicHomeDiscovery extends IPSModule
{
    // Helper Traits
    use MagicHelper;
    use DebugHelper;

    // Discovery constant
    private const DISCOVERY_IP = '255.255.255.255';
    private const DISCOVERY_PORT = 48899;
    private const DISCOVERY_MSG = 'HF-A11ASSISTHREAD';
    private const DISCOVERY_VER = "AT+LVER\r";
    private const DISCOVERY_SEC = 1;

    // Controller ID
    private const MODUL_CONTROLLER_ID = '{E3529714-0243-4D6A-A8F1-899EEF818A1F}';

    /**
     * Create.
     */
    public function Create()
    {
        //Never delete this line!
        parent::Create();
        // Properties
        $this->RegisterPropertyInteger('TargetCategory', 0);
    }

    /**
     * Destroy.
     */
    public function Destroy()
    {
        //Never delete this line!
        parent::Destroy();
    }

    /**
     * Apply Configuration Changes.
     */
    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        //Delete all references in order to readd them
        foreach ($this->GetReferenceList() as $referenceID) {
            $this->UnregisterReference($referenceID);
        }

        // Register reference to categorie
        $this->RegisterReference($this->ReadPropertyInteger('TargetCategory'));
    }

    /**
     * Configuration Form.
     *
     * @return JSON configuration string.
     */
    public function GetConfigurationForm()
    {
        $form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
        $controllers = $this->DiscoverController();
        // Save location
        $location = $this->GetPathOfCategory($this->ReadPropertyInteger('TargetCategory'));
        // Build configuration list values
        if (!empty($controllers)) {
            foreach ($controllers as $controller) {
                $this->SendDebug(__FUNCTION__, $controller);
                $values[] = [
                    'instanceID'    => $this->GetControlerInstances($controller['tcpip']),
                    'tcpip'         => $controller['tcpip'],
                    'macid'         => $controller['mac'],
                    'model'         => $controller['model'],
                    'type'          => MAGIC_HOME_CONTROLLER[$controller['number']][0],
                    'info'          => $controller['info'],
                    'version'       => $controller['version'],
                    'firmware'      => $controller['firmware'],
                    'create'        => [
                        [
                            'moduleID'      => self::MODUL_CONTROLLER_ID,
                            'configuration' => ['TCPIP' => $controller['tcpip'], 'MAC' => $controller['mac'], 'MODEL' => $controller['model'], 'TYPE' => $controller['number']],
                            'location'      => $location,
                        ],
                    ],
                ];
            }
            $form['actions'][0]['values'] = $values;
        }
        return json_encode($form);
    }

    /**
     * Delivers all found controllers.
     *
     * @return array configuration list all controller
     */
    private function DiscoverController()
    {
        // Create UDP Broadcast Socket
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
        socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, ['sec'=>self::DISCOVERY_SEC, 'usec'=>0]);

        // Collect all data
        $data = [];

        // First: Controller Info
        socket_sendto($sock, self::DISCOVERY_MSG, strlen(self::DISCOVERY_MSG), 0, self::DISCOVERY_IP, self::DISCOVERY_PORT);
        while (true) {
            $ret = @socket_recvfrom($sock, $buf, 64, 0, $ip, $port);
            if ($ret === false) {
                break;
            }
            $this->SendDebug(__FUNCTION__, $buf);
            $info = explode(',', $buf);
            $data[] = ['tcpip' => $info[0], 'mac' => $info[1], 'model' => $info[2]];
        }

        $i = 0;
        foreach ($data as $controller) {
            //socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, ['sec'=>1, 'usec'=>0]);
            socket_sendto($sock, self::DISCOVERY_VER, strlen(self::DISCOVERY_VER), 0, $controller['tcpip'], self::DISCOVERY_PORT);
            while (true) {
                $ret = @socket_recvfrom($sock, $buf, 64, 0, $ip, $port);
                if ($ret === false) {
                    break;
                }
                if ($this->StrStartsWith($buf, '+ok=')) {
                    $buf = str_replace("\r", '', $buf);
                    $this->SendDebug(__FUNCTION__, $info);
                    $info = explode('_', $buf);
                    $data[$i]['number'] = intval(substr($info[0], 4), 16);
                    $data[$i]['version'] = intval($info[1], 16);
                    $data[$i]['firmware'] = substr($info[2], 6, 2) . '.' . substr($info[2], 4, 2) . '.' . substr($info[2], 0, 4);
                    $data[$i]['info'] = (isset($info[3])) ? $info[3] : '';
                }
                $i++;
            }
        }
        // close  socket
        socket_close($sock);
        // return list
        $this->SendDebug(__FUNCTION__, $data);
        return $data;
    }

    /**
     * Returns the instance ID for a given controler.
     *
     * @param string device IP adresss
     * @return array device instance id
     */
    private function GetControlerInstances($ip)
    {
        $InstanceIDs = IPS_GetInstanceListByModuleID(self::MODUL_CONTROLLER_ID);
        foreach ($InstanceIDs as $id) {
            if (IPS_GetProperty($id, 'TCPIP') == $ip) {
                return $id;
            }
        }
        return 0;
    }

    /**
     * Returns the ascending list of category names for a given category id
     *
     * @param int $categoryId Category ID.
     * @return array List of reverse catergory names.
     */
    private function GetPathOfCategory(int $categoryId): array
    {
        if ($categoryId === 0) {
            return [];
        }

        $path[] = IPS_GetName($categoryId);
        $parentId = IPS_GetObject($categoryId)['ParentID'];

        while ($parentId > 0) {
            $path[] = IPS_GetName($parentId);
            $parentId = IPS_GetObject($parentId)['ParentID'];
        }

        return array_reverse($path);
    }

    /**
     * Checks if a string starts with a given substring
     *
     * @param string haystack The string to search in.
     * @param string needle The substring to search for in the haystack.
     * @return bool Returns true if haystack begins with needle, false otherwise.
     */
    private function StrStartsWith(string $haystack, string $needle)
    {
        return (string) $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}