<?php

declare(strict_types=1);

/** Generell funktions  */
require_once __DIR__ . '/../libs/_traits.php';

/** Namespaced traits */
use Wilkware\MagicHomeController\DebugHelper;
use Wilkware\MagicHomeController\MagicHelper;

/**
 * CLASS MagicHomeDiscovery
 */
class MagicHomeDiscovery extends IPSModuleStrict
{
    // -------------------------------------------------------------------------
    // Traits
    // -------------------------------------------------------------------------

    use DebugHelper;
    use MagicHelper;

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    /** @var string Discovery IP */
    private const DISCOVERY_IP = '255.255.255.255';

    /** @var int Discovery Port */
    private const DISCOVERY_PORT = 48899;

    /** @var string Discovery Message */
    private const DISCOVERY_MSG = 'HF-A11ASSISTHREAD';

    /** @var string Discovery Version */
    private const DISCOVERY_VER = "AT+LVER\r";

    /** @var int Discovery Timeout for Message */
    private const DISCOVERY_SEM = 1;

    /** @var int Discovery Timeout for Version */
    private const DISCOVERY_SEV = 2;

    /** @var string Controller Module ID */
    private const MODUL_CONTROLLER_ID = '{E3529714-0243-4D6A-A8F1-899EEF818A1F}';

    // -------------------------------------------------------------------------
    // Methods
    // -------------------------------------------------------------------------

    /**
     * In contrast to Construct, this function is called only once when creating the instance and starting IP-Symcon.
     * Therefore, status variables and module properties which the module requires permanently should be created here.
     *
     * @return void
     */
    public function Create(): void
    {
        //Never delete this line!
        parent::Create();

        // Properties
        $this->RegisterPropertyInteger('TargetCategory', 0);
    }

    /**
     * This function is called when deleting the instance during operation and when updating via "Module Control".
     * The function is not called when exiting IP-Symcon.
     *
     * @return void
     */
    public function Destroy(): void
    {
        //Never delete this line!
        parent::Destroy();
    }

    /**
     * The content can be overwritten in order to transfer a self-created configuration page.
     * This way, content can be generated dynamically.
     * In this case, the "form.json" on the file system is completely ignored.
     *
     * @return string Content of the configuration page.
     */
    public function GetConfigurationForm(): string
    {
        $form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);

        // Version check
        $version = (float) IPS_GetKernelVersion();

        // Save location
        $location = $this->GetPathOfCategory($this->ReadPropertyInteger('TargetCategory'));

        // Enable or disable "TargetCategory" for 6.x
        if ($version < 7) {
            $form['elements'][2]['visible'] = true;
        }

        // All installed devices
        $installed = [];
        foreach (IPS_GetInstanceListByModuleID(self::MODUL_CONTROLLER_ID) as $instance) {
            $installed[IPS_GetProperty($instance, 'MAC')] = $instance;
        }

        // Discover controlers
        $controllers = $this->DiscoverController();

        // Collect all values
        $values = [];

        // Build configuration list values
        foreach ($controllers as $controller) {
            $this->LogDebug(__FUNCTION__, $controller);
            // only if we found the type of controller
            if (isset($controller['number'])) {
                $value = [
                    'tcpip'         => $controller['tcpip'],
                    'macid'         => $controller['mac'],
                    'model'         => $controller['model'],
                    'type'          => self::MAGIC_HOME_CONTROLLER[$controller['number']][0],
                    'info'          => $controller['info'],
                    'version'       => $controller['version'],
                    'firmware'      => $controller['firmware'],
                    'create'        => [
                        [
                            'moduleID'      => self::MODUL_CONTROLLER_ID,
                            'configuration' => ['TCPIP' => $controller['tcpip'], 'MAC' => $controller['mac'], 'MODEL' => $controller['model'], 'TYPE' => $controller['number']],
                            'location'      => ($version < 7) ? $location : [],
                        ],
                    ],
                ];
                if (isset($installed[$controller['mac']])) {
                    $value['instanceID'] = $installed[$controller['mac']];
                    // remove it from the list
                    unset($installed[$controller['mac']]);
                } else {
                    $value['instanceID'] = 0;
                }
                $values[] = $value;
            }
        }
        foreach ($installed as $mac => $instance) {
            // However, if an controller is not a discovered device
            $values[] = [
                'macid'         => $mac,
                'tcpip'         => IPS_GetProperty($instance, 'TCPIP'),
                'model'         => IPS_GetProperty($instance, 'MODEL'),
                'type'          => self::MAGIC_HOME_CONTROLLER[IPS_GetProperty($instance, 'TYPE')][0],
                'info'          => '',
                'version'       => ' - ',
                'firmware'      => ' - ',
                'instanceID'    => $instance,
            ];
        }
        // Set available values
        if (!empty($values)) {
            $form['actions'][0]['values'] = $values;
        }
        return json_encode($form);
    }

    /**
     * Is executed when "Apply" is pressed on the configuration page and immediately after the instance has been created.
     *
     * @return void
     */
    public function ApplyChanges(): void
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
     * Delivers all found controllers.
     *
     * @return array<int,mixed> configuration list all controller
     */
    private function DiscoverController(): array
    {
        // Create UDP Broadcast Socket
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
        socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, ['sec'=>self::DISCOVERY_SEM, 'usec'=>0]);

        // Collect all data
        $data = [];

        // First: Controller Info
        socket_sendto($sock, self::DISCOVERY_MSG, strlen(self::DISCOVERY_MSG), 0, self::DISCOVERY_IP, self::DISCOVERY_PORT);
        while (true) {
            $ret = @socket_recvfrom($sock, $buf, 64, 0, $ip, $port);
            if ($ret === false) {
                break;
            }
            $this->LogDebug(__FUNCTION__, $buf); // e.g. '192.168.0.100,43219128B84F,AK001-ZJ210'
            $info = explode(',', $buf);
            $data[] = ['tcpip' => $info[0], 'mac' => $info[1], 'model' => $info[2]];
        }

        $i = 0;
        socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, ['sec'=>self::DISCOVERY_SEV, 'usec'=>0]);
        foreach ($data as $controller) {
            socket_sendto($sock, self::DISCOVERY_VER, strlen(self::DISCOVERY_VER), 0, $controller['tcpip'], self::DISCOVERY_PORT);
            $ret = @socket_recvfrom($sock, $buf, 64, 0, $ip, $port);
            if ($ret === false) {
                // NO DATA
                $this->LogDebug(__FUNCTION__, 'No Version Data for model \'' . $controller['model'] . ' on ' . $controller['tcpip']);
            } else {
                $this->LogDebug(__FUNCTION__, $buf); // '+ok=A1_18_20181031<CR>'
                if (str_starts_with($buf, '+ok=')) {
                    $buf = str_replace("\r", '', $buf); // \r = <CR>
                    $info = explode('_', $buf);
                    $this->LogDebug(__FUNCTION__, $info);
                    $data[$i]['number'] = intval(substr($info[0], 4), 16); // hex
                    $data[$i]['version'] = intval($info[1], 16); // hex
                    $data[$i]['firmware'] = substr($info[2], 6, 2) . '.' . substr($info[2], 4, 2) . '.' . substr($info[2], 0, 4);
                    $data[$i]['info'] = (isset($info[3])) ? $info[3] : '';
                }
            }
            $i++;
        }
        // close  socket
        socket_close($sock);
        // return list
        $this->LogDebug(__FUNCTION__, $data);
        return $data;
    }

    /**
     * Returns the ascending list of category names for a given category id
     *
     * @param int $categoryId Category ID.
     *
     * @return array<string> List of reverse catergory names.
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
}
