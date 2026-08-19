<?php

/**
 * MagicHelper.php
 *
 * PHP Wrapper for Magic Home Controllers.
 *
 * @package       traits
 * @author        Heiko Wilknitz <heiko@wilkware.de>
 * @copyright     2022 Heiko Wilknitz
 * @link          https://wilkware.de
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 */

declare(strict_types=1);

/** @symcon-namespace */

namespace Wilkware\MagicHomeController;

/**
 * Helper class for the debug output.
 */
trait MagicHelper
{
    /** Minimum color temperature (Kelvin). */
    public const MIN_TEMP = 2700;
    /** Maximum color temperature (Kelvin). */
    public const MAX_TEMP = 6500;

    /** Custom effect transition: instant color jump. */
    public const TRANSITION_JUMP = 'jump';
    /** Custom effect transition: strobe. */
    public const TRANSITION_STROBE = 'strobe';
    /** Custom effect transition: gradual fade. */
    public const TRANSITION_GRADUAL = 'gradual';

    /**
     * Protocol byte for each transition type.
     *
     * @var array<string,int>
     */
    public const TRANSITION_BYTES = [
        self::TRANSITION_JUMP    => 0x3B,
        self::TRANSITION_STROBE  => 0x3C,
        self::TRANSITION_GRADUAL => 0x3A,
    ];

    /** Expected response length for a power state message. */
    public const LEDENET_POWER_RESPONSE_LEN = 4;
    /** Expected response length for an 8/9-byte state message. */
    public const LEDENET_STATE_RESPONSE_LEN = 14;
    /** Expected response length for the original protocol's state message. */
    public const LEDENET_STATE_ORIGINAL_RESPONSE_LEN = 11;
    /** Expected response length for an addressable protocol's state message. */
    public const LEDENET_STATE_ADDRESSABLE_RESPONSE_LEN = 25;

    /** Message type: original protocol power state. */
    public const MSG_ORIGINAL_POWER_STATE = 'original_power_state';
    /** Message type: original protocol state. */
    public const MSG_ORIGINAL_STATE = 'original_state';
    /** Message type: power state. */
    public const MSG_POWER_STATE = 'power_state';
    /** Message type: state. */
    public const MSG_STATE = 'state';
    /** Message type: addressable protocol state. */
    public const MSG_ADDRESSABLE_STATE = 'addressable_state';

    /**
     * Maps the first response byte to a message type.
     *
     * @var array<int,string>
     */
    public const MSG_FIRST_BYTE = [
        0xF0 => self::MSG_POWER_STATE,
        0x00 => self::MSG_POWER_STATE,
        0x0F => self::MSG_POWER_STATE,
        0x78 => self::MSG_ORIGINAL_POWER_STATE,
        0x66 => self::MSG_ORIGINAL_STATE,
        0x81 => self::MSG_STATE,
        0xB0 => self::MSG_ADDRESSABLE_STATE,
    ];

    /**
     * Maps a message type to its expected response length.
     *
     * @var array<string,int>
     */
    public const MSG_LENGTHS = [
        self::MSG_POWER_STATE          => self::LEDENET_POWER_RESPONSE_LEN,
        self::MSG_ORIGINAL_POWER_STATE => self::LEDENET_POWER_RESPONSE_LEN,
        self::MSG_ORIGINAL_STATE       => self::LEDENET_STATE_ORIGINAL_RESPONSE_LEN,
        self::MSG_STATE                => self::LEDENET_STATE_RESPONSE_LEN,
        self::MSG_ADDRESSABLE_STATE    => self::LEDENET_STATE_ADDRESSABLE_RESPONSE_LEN,
    ];

    /** Custom effect code. */
    public const EFFECT_CUSTOM_CODE = 0x60;
    /** Preset music mode code. */
    public const PRESET_MUSIC_MODE = 0x62;

    /** Protocol id: original LEDENET protocol (no checksum). */
    public const PROTOCOL_LEDENET_ORIGINAL = 0;
    /** Protocol id: 9-byte LEDENET protocol. */
    public const PROTOCOL_LEDENET_9BYTE = 1;
    /** Protocol id: 9-byte LEDENET protocol with dimmable effects. */
    public const PROTOCOL_LEDENET_9BYTE_DIMMABLE_EFFECTS = 2;
    /** Protocol id: 8-byte LEDENET protocol. */
    public const PROTOCOL_LEDENET_8BYTE = 3;
    /** Protocol id: 8-byte LEDENET protocol with dimmable effects. */
    public const PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS = 4;
    /** Protocol id: addressable protocol A1. */
    public const PROTOCOL_LEDENET_ADDRESSABLE_A1 = 5;
    /** Protocol id: addressable protocol A2. */
    public const PROTOCOL_LEDENET_ADDRESSABLE_A2 = 6;
    /** Protocol id: addressable protocol A3. */
    public const PROTOCOL_LEDENET_ADDRESSABLE_A3 = 7;
    /** Protocol id: CCT protocol. */
    public const PROTOCOL_LEDENET_CCT = 8;

    /**
     * Maps a protocol id to its implementing class name.
     *
     * @var array<int,class-string>
     */
    public const CLASS_PROTOCOL = [
        self::PROTOCOL_LEDENET_ORIGINAL               => ProtocolLEDENETOriginal::class,
        self::PROTOCOL_LEDENET_9BYTE                  => ProtocolLEDENET9Byte::class,
        self::PROTOCOL_LEDENET_9BYTE_DIMMABLE_EFFECTS => ProtocolLEDENET9ByteDimmableEffects::class,
        self::PROTOCOL_LEDENET_8BYTE                  => ProtocolLEDENET8Byte::class,
        self::PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS => ProtocolLEDENET8ByteDimmableEffects::class,
        self::PROTOCOL_LEDENET_ADDRESSABLE_A1         => ProtocolLEDENETAddressableA1::class,
        self::PROTOCOL_LEDENET_ADDRESSABLE_A2         => ProtocolLEDENETAddressableA2::class,
        self::PROTOCOL_LEDENET_ADDRESSABLE_A3         => ProtocolLEDENETAddressableA3::class,
        self::PROTOCOL_LEDENET_CCT                    => ProtocolLEDENETCCT::class,
    ];

    /**
     * Protocol ids that are addressable.
     *
     * @var int[]
     */
    public const ADDRESSABLE_PROTOCOLS = [
        self::PROTOCOL_LEDENET_ADDRESSABLE_A1,
        self::PROTOCOL_LEDENET_ADDRESSABLE_A2,
        self::PROTOCOL_LEDENET_ADDRESSABLE_A3,
    ];

    /**
     * Protocol ids that support the "original" effects preset list.
     *
     * @var int[]
     */
    public const ORIGINAL_EFFECTS_PROTOCOLS = [
        self::PROTOCOL_LEDENET_ADDRESSABLE_A1,
    ];

    /**
     * Protocol ids that support the addressable effects preset list.
     *
     * @var int[]
     */
    public const ADDRESSABLE_EFFECTS_PROTOCOLS = [
        self::PROTOCOL_LEDENET_ADDRESSABLE_A2,
        self::PROTOCOL_LEDENET_ADDRESSABLE_A3,
    ];

    /**
     * Protocol ids that support dimmable (brightness-adjustable) effects.
     *
     * @var int[]
     */
    public const BRIGHTNESS_EFFECTS_PROTOCOLS = [
        self::PROTOCOL_LEDENET_9BYTE_DIMMABLE_EFFECTS,
        self::PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS,
        self::PROTOCOL_LEDENET_ADDRESSABLE_A2,
        self::PROTOCOL_LEDENET_ADDRESSABLE_A3,
    ];

    /**
     * Known controller models, keyed by model byte.
     *
     * @var array<int,array{0:string,1:int,2:bool}> Each entry is [name, protocol id, supports write-white].
     */
    public const MAGIC_HOME_CONTROLLER = [
        0x01 => ['Original LEDENET', self::PROTOCOL_LEDENET_ORIGINAL, false],
        0x04 => ['UFO LED WiFi Controller', self::PROTOCOL_LEDENET_8BYTE, true],
        0x06 => ['RGBW Controller', self::PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS, false],
        0x07 => ['RGBCW Controller', self::PROTOCOL_LEDENET_9BYTE_DIMMABLE_EFFECTS, false],
        0x08 => ['RGB Controller with MIC', self::PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS, true],
        0x09 => ['CCT Ceiling Light', self::PROTOCOL_LEDENET_8BYTE, false],
        0x0B => ['Smart Switch 1c', self::PROTOCOL_LEDENET_8BYTE, false],
        0x0E => ['Floor Lamp', self::PROTOCOL_LEDENET_9BYTE, false],
        0x10 => ['Christmas Light', self::PROTOCOL_LEDENET_8BYTE, false],
        0x16 => ['Magnetic Light CCT', self::PROTOCOL_LEDENET_8BYTE, false],
        0x17 => ['Magnetic Light Dimable', self::PROTOCOL_LEDENET_8BYTE, false],
        0x18 => ['Plant Light', self::PROTOCOL_LEDENET_8BYTE, false],
        0x19 => ['Smart Socket 2 USB', self::PROTOCOL_LEDENET_8BYTE, false],
        0x1A => ['Christmas Light', self::PROTOCOL_LEDENET_8BYTE, false],
        0x1B => ['Spray Light', self::PROTOCOL_LEDENET_8BYTE, false],
        0x1C => ['Table Light CCT', self::PROTOCOL_LEDENET_CCT, false],
        0x21 => ['Smart Bulb Dimmable', self::PROTOCOL_LEDENET_8BYTE, true],
        0x25 => ['RGB/WW/CW Controller', self::PROTOCOL_LEDENET_9BYTE, false],
        0x33 => ['RGB Controller', self::PROTOCOL_LEDENET_8BYTE, true],
        0x35 => ['Smart Bulb RGBCW', self::PROTOCOL_LEDENET_9BYTE, false],
        0x41 => ['Single Channel Controller', self::PROTOCOL_LEDENET_8BYTE, false],
        0x44 => ['Smart Bulb RGBW', self::PROTOCOL_LEDENET_8BYTE, false],
        0x45 => ['Unknown', self::PROTOCOL_LEDENET_8BYTE, false],
        0x52 => ['Smart Bulb CCT', self::PROTOCOL_LEDENET_8BYTE, false],
        0x54 => ['Downlight RGBW', self::PROTOCOL_LEDENET_8BYTE, false],
        0x62 => ['CCT Controller', self::PROTOCOL_LEDENET_8BYTE, false],
        0x81 => ['Unknown', self::PROTOCOL_LEDENET_8BYTE, true],
        0x93 => ['Smart Switch 1C', self::PROTOCOL_LEDENET_8BYTE, false],
        0x94 => ['Smart Switch 1c Watt', self::PROTOCOL_LEDENET_8BYTE, false],
        0x95 => ['Smart Switch 2c', self::PROTOCOL_LEDENET_8BYTE, false],
        0x96 => ['Smart Switch 4c', self::PROTOCOL_LEDENET_8BYTE, false],
        0x97 => ['Smart Socket 1c', self::PROTOCOL_LEDENET_8BYTE, false],
        0xA1 => ['RGB Symphony v1', self::PROTOCOL_LEDENET_ADDRESSABLE_A1, false],
        0xA2 => ['RGB Symphony v2', self::PROTOCOL_LEDENET_ADDRESSABLE_A2, false],
        0xA3 => ['RGB Symphony v3', self::PROTOCOL_LEDENET_ADDRESSABLE_A3, false],
        0xD1 => ['Digital Light', self::PROTOCOL_LEDENET_8BYTE, false],
        0xE1 => ['Ceiling Light', self::PROTOCOL_LEDENET_8BYTE, false],
        0xE2 => ['Ceiling Light Assist', self::PROTOCOL_LEDENET_8BYTE, false],
    ];

    /**
     * Model bytes of devices that are switches/sockets rather than lights.
     *
     * @var int[]
     */
    public const MAGIC_HOME_SWITCHES = [0x19, 0x93, 0x0B, 0x93, 0x94, 0x95, 0x96, 0x97];
}

/**
 * Abstract base class implementing the common protocol logic shared by all
 * concrete Magic Home protocol variants.
 */
abstract class ProtocolBase
{
    use MagicHelper;

    /** Byte value that switches the device on. */
    protected int $powerOn = 0x23;
    /** Byte value that switches the device off. */
    protected int $powerOff = 0x24;
    /** Running counter used by addressable protocols to tag messages. */
    protected int $counter = 0;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->counter = 0;
    }

    /**
     * Protocol supports dimmable effects.
     *
     * @return bool True if dimmable effects are supported, false otherwise.
     */
    public function DimmableEffects(): bool
    {
        return false;
    }

    /**
     * The length of the query response.
     *
     * @return int Length of the query response.
     */
    public function StateResponseLength(): int
    {
        return self::LEDENET_POWER_RESPONSE_LEN;
    }

    /**
     * The bytes to send for a preset pattern.
     *
     * @param int $pattern    Pattern number.
     * @param int $speed      Speed (0-100).
     * @param int $brightness Brightness (0-255).
     *
     * @return int[] Message bytes.
     */
    public function ConstructPresetPattern(int $pattern, int $speed, int $brightness): array
    {
        $delay = self::SpeedToDelay($speed);
        $msg = [0x61, $pattern, $delay, 0x0F];
        return $this->ConstructMessage($msg);
    }

    /**
     * The bytes to send for a custom effect.
     *
     * @param array<array{0: int, 1: int, 2: int}> $rgblist  List of [red, green, blue] tuples.
     * @param int    $speed    Speed (0-100).
     * @param string $transtype Transition type (see TRANSITION_* constants).
     *
     * @return int[] Message bytes.
     */
    public function ConstructCustomEffect(array $rgblist, int $speed, string $transtype): array
    {
        $msg = [];
        $lead_byte = 0x00;
        $first_color = true;
        foreach ($rgblist as $rgb) {
            if ($first_color) {
                $lead_byte = 0x51;
                $first_color = false;
            } else {
                $lead_byte = 0;
            }
            [$r, $g, $b] = $rgb;

            $msg[] = $lead_byte;
            $msg[] = $r;
            $msg[] = $g;
            $msg[] = $b;
        }
        // pad out empty slots
        if (count($rgblist) != 16) {
            $len = 16 - count($rgblist);
            for ($i = 0; $i < $len; $i++) {
                $msg[] = 0;
                $msg[] = 1;
                $msg[] = 2;
                $msg[] = 3;
            }
        }
        $msg[] = 0x00;
        $msg[] = self::SpeedToDelay($speed);
        $msg[] = self::TRANSITION_BYTES[$transtype] ?? self::TRANSITION_BYTES[self::TRANSITION_GRADUAL]; # default to "gradual"
        $msg[] = 0xFF;
        $msg[] = 0x0F;
        return $this->ConstructMessage($msg);
    }

    /**
     * The name of the protocol (the implementing class name).
     *
     * @return string Name of the protocol.
     */
    public function Name(): string
    {
        return self::CLASS_PROTOCOL[$this->Id()];
    }

    /**
     * The ID of the protocol.
     *
     * @return int ID of the protocol.
     */
    abstract public function Id(): int;

    /**
     * The bytes to send for a query request.
     *
     * @return int[] Message bytes.
     */
    abstract public function ConstructStateQuery(): array;

    /**
     * The bytes to send for a state change request.
     *
     * @param bool $turn Turn on (true) or off (false).
     *
     * @return int[] Message bytes.
     */
    abstract public function ConstructStateChange(bool $turn): array;

    /**
     * The bytes to send for a level change request.
     *
     * @param bool $persist    Persist the change.
     * @param int  $red        Red value (0-255).
     * @param int  $green      Green value (0-255).
     * @param int  $blue       Blue value (0-255).
     * @param int  $warmwhite  Warm white value (0-255).
     * @param int  $coolwhite  Cool white value (0-255).
     * @param int  $colormask  Write mode / color mask.
     *
     * @return int[] Message bytes.
     */
    abstract public function ConstructLevelsChange(bool $persist, int $red, int $green, int $blue, int $warmwhite, int $coolwhite, int $colormask): array;

    /**
     * Check if a state response is valid.
     *
     * @param int[] $raw Raw response bytes.
     *
     * @return bool True if the state response is valid, false otherwise.
     */
    abstract public function IsValidStateResponse(array $raw): bool;

    /**
     * Converts a speed (0-100) into a protocol delay value (1-31).
     *
     * @param int $speed Speed value (0-100).
     *
     * @return int Delay value (1-31).
     */
    public static function SpeedToDelay(int $speed): int
    {
        # speed is 0-100, delay is 1-31
        $speed = max(0, min(100, $speed));
        $inv_speed = 100 - $speed;
        $delay = intval(($inv_speed * (0x1F - 1)) / 100);
        # translate from 0-30 to 1-31
        $delay = $delay + 1;
        return $delay;
    }

    /**
     * Converts a protocol delay value (1-31) into a speed (0-100).
     *
     * @param int $delay Delay value (1-31).
     *
     * @return int Speed value (0-100).
     */
    public static function DelayToSpeed(int $delay): int
    {
        # speed is 0-100, delay is 1-31
        # 1st translate delay to 0-30
        $delay = $delay - 1;
        $delay = max(0, min(0x1F - 1, $delay));
        $inv_speed = intval(($delay * 100) / (0x1F - 1));
        $speed = 100 - $inv_speed;
        return $speed;
    }

    /**
     * Returns Scaled Color Temperature.
     *
     * @param int $warmwhite Warm white value (0-255).
     * @param int $coolwhite Cool white value (0-255).
     *
     * @return array{0: int, 1: int} Tuple of [scaled temperature, brightness].
     */
    protected function WhiteLevelsToScaledColorTemp(int $warmwhite, int $coolwhite): array
    {
        if (($warmwhite <= 0) || ($warmwhite >= 255)) {
            throw new \Exception('Warm White of {warm_white} is not valid and must be between 0 and 255');
        }
        if (($coolwhite <= 0) || ($coolwhite >= 255)) {
            throw new \Exception('Cool White of {cool_white} is not valid and must be between 0 and 255');
        }
        $warm = $warmwhite / 255;
        $cold = $coolwhite / 255;
        $brightness = $warm + $cold;
        if ($brightness == 0) {
            $temperature = 0;
        } else {
            $temperature = ($cold / $brightness) * 100;
        }
        return [(int) round($temperature), (int) min(100, round($brightness * 100))];
    }

    /**
     * Increment the counter byte.
     *
     * @return int The new counter value.
     */
    protected function IncrementCounter(): int
    {
        $this->counter += 1;
        return $this->counter;
    }

    /**
     * Check if a message is the start of an addressable state response.
     *
     * @param int[] $data Raw response bytes.
     *
     * @return bool True if the message is the start of an addressable state response, false otherwise.
     */
    protected function IsStartOfAddressableResponse(array $data): bool
    {
        return false;
    }

    /**
     * Check if a message is a valid addressable state response.
     *
     * @param int[] $data Raw response bytes.
     *
     * @return bool True if the message is a valid addressable state response, false otherwise.
     */
    protected function IsValidAddressableResponse(array $data): bool
    {
        return false;
    }

    /**
     * Return the number of bytes expected in the response.
     * If the response is unknown, we assume the response is
     * a complete message since we have no way of knowing otherwise.
     *
     * @param int[] $data Raw response bytes.
     *
     * @return int Expected response length.
     */
    protected function ExpectedResponseLength(array $data): int
    {
        $type = self::MSG_FIRST_BYTE[$data[0]] ?? null;
        if ($type === null) {
            return count($data);
        }
        return self::MSG_LENGTHS[$type];
    }

    /**
     * Check a checksum of a message.
     *
     * @param int[] $msg Message bytes (last byte is the checksum).
     *
     * @return bool True if the checksum is correct, false otherwise.
     */
    protected function IsChecksumCorrect(array $msg): bool
    {
        $expected = array_sum(array_slice($msg, 0, -1)) & 0xFF;
        if ($expected != $msg[count($msg) - 1]) {
            return false;
        }
        return true;
    }

    /**
     * Original protocol uses no checksum.
     *
     * @param int[] $raw Message bytes.
     *
     * @return int[] Message bytes (with checksum appended, if applicable).
     */
    abstract protected function ConstructMessage(array $raw): array;

    /**
     * Check if a message is the start of a state response.
     *
     * @param int[] $data Raw response bytes.
     *
     * @return bool True if the message is the start of a state response, false otherwise.
     */
    abstract protected function IsStartOfStateResponse(array $data): bool;

    /**
     * Check if a power state response is valid.
     *
     * @param int[] $msg Raw response bytes.
     *
     * @return bool True if the power state response is valid, false otherwise.
     */
    abstract protected function IsValidPowerStateResponse(array $msg): bool;

    /**
     * Check if a message is the start of a power response.
     *
     * @param int[] $data Raw response bytes.
     *
     * @return bool True if the message is the start of a power response, false otherwise.
     */
    abstract protected function IsStartOfPowerStateResponse(array $data): bool;
}

/**
 * The original LEDENET protocol with no checksums.
 */
class ProtocolLEDENETOriginal extends ProtocolBase
{
    /**
     * The id of the protocol.
     *
     * @return int ID of the protocol.
     */
    public function Id(): int
    {
        return self::PROTOCOL_LEDENET_ORIGINAL;
    }

    /**
     * The length of the query response.
     *
     * @return int Expected response length.
     */
    public function StateResponseLength(): int
    {
        return self::LEDENET_STATE_ORIGINAL_RESPONSE_LEN;
    }

    /**
     * The bytes to send for a query request.
     *
     * @return int[] Message bytes.
     */
    public function ConstructStateQuery(): array
    {
        $msg = [0xEF, 0x01, 0x77];
        return $this->ConstructMessage($msg);
    }

    /**
     * The bytes to send for a state change request.
     *
     * @param bool $turn Turn on (true) or off (false).
     *
     * @return int[] Message bytes.
     */
    public function ConstructStateChange(bool $turn): array
    {
        $msg = [0xCC, $turn ? $this->powerOn : $this->powerOff, 0x33];
        return $this->ConstructMessage($msg);
    }

    /**
     * The bytes to send for a level change request.
     *
     * sample message for original LEDENET protocol (w/o checksum at end)
     *  0  1  2  3  4
     *  56 90 fa 77 aa
     *  |  |  |  |  |
     *  |  |  |  |  terminator
     *  |  |  |  blue
     *  |  |  green
     *  |  red
     *  head
     *
     * @param bool $persist    Persist the change.
     * @param int  $red        Red value (0-255).
     * @param int  $green      Green value (0-255).
     * @param int  $blue       Blue value (0-255).
     * @param int  $warmwhite  Warm white value (0-255).
     * @param int  $coolwhite  Cool white value (0-255).
     * @param int  $colormask  Color mask.
     *
     * @return int[] Message bytes.
     */
    public function ConstructLevelsChange(bool $persist, int $red, int $green, int $blue, int $warmwhite, int $coolwhite, int $colormask): array
    {
        $msg = [0x56, $red, $green, $blue, 0xAA];
        return $this->ConstructMessage($msg);
    }

    /**
     * Check if a state response is valid.
     *
     * @param int[] $raw Raw response bytes.
     *
     * @return bool True if the state response is valid, false otherwise.
     */
    public function IsValidStateResponse(array $raw): bool
    {
        return (count($raw) == $this->StateResponseLength()) && ($raw[0] == 0x66) && ($raw[1] == 0x01);
    }

    /**
     * Check if a power state response is valid.
     *
     * @param int[] $msg Raw response bytes.
     *
     * @return bool True if the power state response is valid, false otherwise.
     */
    protected function IsValidPowerStateResponse(array $msg): bool
    {
        return (count($msg) == $this->StateResponseLength()) && ($msg[0] == 0x78);
    }

    /**
     * Check if a message is the start of a state response.
     *
     * @param int[] $data Raw response bytes.
     *
     * @return bool True if the message is the start of a state response, false otherwise.
     */
    protected function IsStartOfStateResponse(array $data): bool
    {
        return $data[0] == 0x66;
    }

    /**
     * Check if a message is the start of a state response.
     *
     * @param int[] $data Raw response bytes.
     *
     * @return bool True if the message is the start of a state response, false otherwise.
     */
    protected function IsStartOfPowerStateResponse(array $data): bool
    {
        return $data[0] == 0x78;
    }

    /**
     * Original protocol uses no checksum.
     *
     * @param int[] $raw Message bytes.
     *
     * @return int[] Message bytes.
     */
    protected function ConstructMessage(array $raw): array
    {
        return $raw;
    }
}

/**
 * The newer LEDENET protocol with checksums that uses 8 bytes to set state.
 */
class ProtocolLEDENET8Byte extends ProtocolBase
{
    /** @var int[] Header bytes identifying an addressable protocol message. */
    protected array $ADDRESSABLE_HEADER = [0xB0, 0xB1, 0xB2, 0xB3, 0x00, 0x01, 0x01];
    /** Expected response length for an addressable state message. */
    protected int $addressableResponseLength = self::LEDENET_STATE_ADDRESSABLE_RESPONSE_LEN;

    /**
     * The id of the protocol.
     *
     * @return int ID of the protocol.
     */
    public function Id(): int
    {
        return self::PROTOCOL_LEDENET_8BYTE;
    }

    /**
     * The length of the query response.
     *
     * @return int Expected response length.
     */
    public function StateResponseLength(): int
    {
        return self::LEDENET_STATE_RESPONSE_LEN;
    }

    /**
     * The bytes to send for a query request.
     *
     * @return int[] Message bytes.
     */
    public function ConstructStateQuery(): array
    {
        $msg = [0x81, 0x8A, 0x8B];
        return $this->ConstructMessage($msg);
    }

    /**
     * The bytes to send for a state change request.
     *
     * Alternate messages
     * Off 3b 24 00 00 00 00 00 00 00 32 00 00 91
     * On  3b 23 00 00 00 00 00 00 00 32 00 00 90
     *
     * @param bool $turn Turn on (true) or off (false).
     *
     * @return int[] Message bytes.
     */
    public function ConstructStateChange(bool $turn): array
    {
        $msg = [0x71, $turn ? $this->powerOn : $this->powerOff, 0x0F];
        return $this->ConstructMessage($msg);
    }

    /**
     * The bytes to send for a level change request.
     *
     * sample message for 8-byte protocols (w/ checksum at end)
     *  0  1  2  3  4  5  6
     * 31 90 fa 77 00 00 0f
     *  |  |  |  |  |  |  |
     *  |  |  |  |  |  |  terminator
     *  |  |  |  |  |  write mask / white2 (see below)
     *  |  |  |  |  white
     *  |  |  |  blue
     *  |  |  green
     *  |  red
     *  persistence (31 for true / 41 for false)
     *
     * byte 5 can have different values depending on the type
     * of device:
     * For devices that support 2 types of white value (warm and cold
     * white) this value is the cold white value. These use the LEDENET
     * protocol. If a second value is not given, reuse the first white value.
     *
     * For devices that cannot set both rbg and white values at the same time
     * (including devices that only support white) this value
     * specifies if this command is to set white value (0f) or the rgb
     * value (f0).
     *
     * For all other rgb and rgbw devices, the value is 00
     *
     * @param bool $persist    Persist the change.
     * @param int  $red        Red value (0-255).
     * @param int  $green      Green value (0-255).
     * @param int  $blue       Blue value (0-255).
     * @param int  $warmwhite  Warm white value (0-255).
     * @param int  $coolwhite  Cool white value (0-255).
     * @param int  $writemode  Write mode / color mask.
     *
     * @return int[] Message bytes.
     */
    public function ConstructLevelsChange(bool $persist, int $red, int $green, int $blue, int $warmwhite, int $coolwhite, int $writemode): array
    {
        $msg = [$persist ? 0x31 : 0x41, $red, $green, $blue, $warmwhite, $writemode, 0x0F];
        return $this->ConstructMessage($msg);
    }

    /**
     * Check if a state response is valid.
     *
     * @param int[] $raw Raw response bytes.
     *
     * @return bool True if the state response is valid, false otherwise.
     */
    public function IsValidStateResponse(array $raw): bool
    {
        if (count($raw) != $this->StateResponseLength()) {
            return false;
        }
        if (!$this->IsStartOfStateResponse($raw)) {
            return false;
        }
        return $this->IsChecksumCorrect($raw);
    }

    /**
     * Check if a power state response is valid.
     *
     * @param int[] $msg Raw response bytes.
     *
     * @return bool True if the power state response is valid, false otherwise.
     */
    protected function IsValidPowerStateResponse(array $msg): bool
    {
        if ((count($msg) != self::LEDENET_POWER_RESPONSE_LEN) || !($this->IsStartOfPowerStateResponse($msg)) || ($msg[1] != 0x71) || (($msg[2] != $this->powerOn) && ($msg[2] != $this->powerOff))) {
            return false;
        }
        return $this->IsChecksumCorrect($msg);
    }

    /**
     * Check if a message is the start of a state response.
     *
     * @param int[] $data Raw response bytes.
     *
     * @return bool True if the message is the start of a state response, false otherwise.
     */
    protected function IsStartOfPowerStateResponse(array $data): bool
    {
        return (count($data) >= 1) && ((self::MSG_FIRST_BYTE[$data[0]] ?? null) === self::MSG_POWER_STATE);
    }

    /**
     * Check if a message is the start of a state response.
     *
     * @param int[] $data Raw response bytes.
     *
     * @return bool True if the message is the start of a state response, false otherwise.
     */
    protected function IsStartOfStateResponse(array $data): bool
    {
        return $data[0] == 0x81;
    }

    /**
     * Calculate checksum of byte array and add to end.
     *
     * @param int[] $raw Message bytes.
     *
     * @return int[] Message bytes with checksum appended.
     */
    protected function ConstructMessage(array $raw): array
    {
        $checksum = array_sum($raw) & 0xFF;
        $raw[] = $checksum;
        return $raw;
    }

    /**
     * Check if a message is the start of an addressable state response.
     *
     * @param int[] $data Raw response bytes.
     *
     * @return bool True if the message is the start of an addressable state response, false otherwise.
     */
    protected function IsStartOfAddressableResponse(array $data): bool
    {
        $i = 0;
        for ($i = 0; $i < count($this->ADDRESSABLE_HEADER); $i++) {
            if ($data[$i] != $this->ADDRESSABLE_HEADER[$i]) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if a message is a valid addressable state response.
     *
     * @param int[] $data Raw response bytes.
     *
     * @return bool True if the message is a valid addressable state response, false otherwise.
     */
    protected function IsValidAddressableResponse(array $data): bool
    {
        if (count($data) != $this->addressableResponseLength) {
            return false;
        }
        if (!$this->IsStartOfAddressableResponse($data)) {
            return false;
        }
        return $this->IsChecksumCorrect($data);
    }
}

/**
 * The LEDENET protocol with checksums that uses 9 bytes to set state.
 */
class ProtocolLEDENET8ByteDimmableEffects extends ProtocolLEDENET8Byte
{
    /**
     * The id of the protocol.
     *
     * @return int ID of the protocol.
     */
    public function Id(): int
    {
        return self::PROTOCOL_LEDENET_8BYTE_DIMMABLE_EFFECTS;
    }

    /**
     * Protocol supports dimmable effects.
     *
     * @return bool True if dimmable effects are supported, false otherwise.
     */
    public function DimmableEffects(): bool
    {
        return true;
    }

    /**
     * The bytes to send for a preset pattern.
     *
     * @param int $pattern    Pattern number.
     * @param int $speed      Speed (0-100).
     * @param int $brightness Brightness (0-255).
     *
     * @return int[] Message bytes.
     */
    public function ConstructPresetPattern(int $pattern, int $speed, int $brightness): array
    {
        $delay = self::SpeedToDelay($speed);
        $msg = [0x38, $pattern, $delay, $brightness];
        return $this->ConstructMessage($msg);
    }
}

/**
 * The newer LEDENET protocol with checksums that uses 9 bytes to set state.
 */
class ProtocolLEDENET9Byte extends ProtocolLEDENET8Byte
{
    /**
     * The id of the protocol.
     *
     * @return int ID of the protocol.
     */
    public function Id(): int
    {
        return self::PROTOCOL_LEDENET_9BYTE;
    }

    /**
     * The bytes to send for a level change request.
     *
     * sample message for 9-byte LEDENET protocol (w/ checksum at end)
     *  0  1  2  3  4  5  6  7
     * 31 bc c1 ff 00 00 f0 0f
     *  |  |  |  |  |  |  |  |
     *  |  |  |  |  |  |  |  terminator
     *  |  |  |  |  |  |  write mode (f0 colors, 0f whites, 00 colors & whites)
     *  |  |  |  |  |  cold white
     *  |  |  |  |  warm white
     *  |  |  |  blue
     *  |  |  green
     *  |  red
     *  persistence (31 for true / 41 for false)
     *
     * @param bool $persist    Persist the change.
     * @param int  $red        Red value (0-255).
     * @param int  $green      Green value (0-255).
     * @param int  $blue       Blue value (0-255).
     * @param int  $warmwhite  Warm white value (0-255).
     * @param int  $coolwhite  Cool white value (0-255).
     * @param int  $writemode  Write mode / color mask.
     *
     * @return int[] Message bytes.
     */
    public function ConstructLevelsChange(bool $persist, int $red, int $green, int $blue, int $warmwhite, int $coolwhite, int $writemode): array
    {
        $msg = [$persist ? 0x31 : 0x41, $red, $green, $blue, $warmwhite, $coolwhite, $writemode, 0x0F];
        return $this->ConstructMessage($msg);
    }
}

/**
 * The newer LEDENET protocol with checksums that uses 9 bytes to set state
 * and supports dimmable effects.
 */
class ProtocolLEDENET9ByteDimmableEffects extends ProtocolLEDENET9Byte
{
    /**
     * The id of the protocol.
     *
     * @return int ID of the protocol.
     */
    public function Id(): int
    {
        return self::PROTOCOL_LEDENET_9BYTE_DIMMABLE_EFFECTS;
    }

    /**
     * Protocol supports dimmable effects.
     *
     * @return bool True if dimmable effects are supported, false otherwise.
     */
    public function DimmableEffects(): bool
    {
        return true;
    }

    /**
     * The bytes to send for a preset pattern.
     *
     * @param int $pattern    Pattern number.
     * @param int $speed      Speed (0-100).
     * @param int $brightness Brightness (0-255).
     *
     * @return int[] Message bytes.
     */
    public function ConstructPresetPattern(int $pattern, int $speed, int $brightness): array
    {
        $delay = self::SpeedToDelay($speed);
        $msg = [0x38, $pattern, $delay, $brightness];
        return $this->ConstructMessage($msg);
    }
}

/**
 * The newer LEDENET addressable protocol A1.
 */
class ProtocolLEDENETAddressableA1 extends ProtocolLEDENET9Byte
{
    /**
     * The id of the protocol.
     *
     * @return int ID of the protocol.
     */
    public function Id(): int
    {
        return self::PROTOCOL_LEDENET_ADDRESSABLE_A1;
    }

    /**
     * Protocol supports dimmable effects.
     *
     * @return bool True if dimmable effects are supported, false otherwise.
     */
    public function DimmableEffects(): bool
    {
        return false;
    }

    /**
     * The bytes to send for a preset pattern.
     *
     * @param int $pattern    Pattern number.
     * @param int $speed      Speed (0-100).
     * @param int $brightness Brightness (0-255).
     *
     * @return int[] Message bytes.
     */
    public function ConstructPresetPattern(int $pattern, int $speed, int $brightness): array
    {
        $effect = $pattern + 99;
        $msg = [0x61, $effect >> 8, $effect & 0xFF, $speed, 0x0F];
        return $this->ConstructMessage($msg);
    }
}

/**
 * The newer LEDENET addressable protocol A2.
 */
class ProtocolLEDENETAddressableA2 extends ProtocolLEDENET9Byte
{
    /**
     * The id of the protocol.
     *
     * @return int ID of the protocol.
     */
    public function Id(): int
    {
        return self::PROTOCOL_LEDENET_ADDRESSABLE_A2;
    }

    /**
     * Protocol supports dimmable effects.
     *
     * @return bool True if dimmable effects are supported, false otherwise.
     */
    public function DimmableEffects(): bool
    {
        return true;
    }

    // white  41 01 ff ff ff 00 00 00 60 ff 00 00 9e
    /**
     * The bytes to send for a level change request.
     *
     * @param bool $persist     Whether to persist the changes.
     * @param int  $red         Red value (0-255).
     * @param int  $green       Green value (0-255).
     * @param int  $blue        Blue value (0-255).
     * @param int  $warmwhite   Warm white value (0-255).
     * @param int  $coolwhite   Cool white value (0-255).
     * @param int  $writemode   Write mode / color mask.
     *
     * @return int[] Message bytes.
     */
    public function ConstructLevelsChange(bool $persist, int $red, int $green, int $blue, int $warmwhite, int $coolwhite, int $writemode): array
    {
        $preset_number = 0x01;  # aka fixed color
        $msg = [0x41, $preset_number, $red, $green, $blue, 0x00, 0x00, 0x00, 0x60, 0xFF, 0x00, 0x00];
        return $this->ConstructMessage($msg);
    }

    /**
     * The bytes to send for a preset pattern.
     *
     * @param int $pattern    Pattern number.
     * @param int $speed      Speed (0-100).
     * @param int $brightness Brightness (0-255).
     *
     * @return int[] Message bytes.
     */
    public function ConstructPresetPattern(int $pattern, int $speed, int $brightness): array
    {
        $msg = [0x42, $pattern, $speed, $brightness];
        return $this->ConstructMessage($msg);
    }
}

/**
 * The newer LEDENET addressable protocol A3.
 */
class ProtocolLEDENETAddressableA3 extends ProtocolLEDENET9Byte
{
    /**
     * The id of the protocol.
     *
     * @return int ID of the protocol.
     */
    public function Id(): int
    {
        return self::PROTOCOL_LEDENET_ADDRESSABLE_A3;
    }

    /**
     * Protocol supports dimmable effects.
     *
     * @return bool True if dimmable effects are supported, false otherwise.
     */
    public function DimmableEffects(): bool
    {
        return true;
    }

    /**
     * The bytes to send for a level change request.
     *
     * b0 [unknown static?] b1 [unknown static?] b2 [unknown static?] b3 [unknown static?] 00 [unknown static?] 01 [unknown static?] 01 [unknown static?] 6a [incrementing sequence number] 00 [unknown static?] 0d [unknown, sometimes 0c] 41 [unknown static?] 02 [preset number] ff [foreground r] 00 [foreground g] 00 [foreground b] 00 [background red] ff [background green] 00 [background blue] 06 [speed or direction?] 00 [unknown static?] 00 [unknown static?] 00 [unknown static?] 47 [speed or direction?] cd [check sum]
     * Known messages
     * b0 b1 b2 b3 00 01 01 01 00 0c 10 14 15 0a 0b 0e 12 06 01 00 0f 84 dd - preset 1
     * b0 b1 b2 b3 00 01 01 03 00 0d 41 02 00 ff ff 00 00 00 06 00 00 00 47 66 - preset 2
     * b0 b1 b2 b3 00 01 01 04 00 0d 41 03 00 ff ff 00 00 00 06 00 00 00 48 69 - preset 3
     * b0 b1 b2 b3 00 01 01 02 00 0d 41 01 00 ff ff 00 00 00 06 ff 00 00 45 61 - preset 4
     * b0 b1 b2 b3 00 01 01 1f 00 0d 41 01 ff 00 00 00 00 00 06 ff 00 00 46 80 - preset 1 red or green
     * b0 b1 b2 b3 00 01 01 27 00 0d 41 01 00 ff 00 00 00 00 06 ff 00 00 46 88 - preset 1 red or green
     * b0 b1 b2 b3 00 01 01 2e 00 0d 41 01 ff 00 00 00 00 00 06 ff 00 00 46 8f - preset 1 red (foreground)
     * b0 b1 b2 b3 00 01 01 27 00 0d 41 01 00 ff 00 00 00 00 06 ff 00 00 46 88 - preset 1 green (foreground)
     * b0 b1 b2 b3 00 01 01 3e 00 0d 41 01 00 00 ff 00 00 00 06 ff 00 00 46 9f - preset 1 blue (foreground)
     * b0 b1 b2 b3 00 01 01 54 00 0d 41 02 00 ff 00 00 00 00 06 00 00 00 48 b9 - preset 2 green (foreground)
     * b0 b1 b2 b3 00 01 01 55 00 0d 41 02 ff 00 00 00 00 00 06 00 00 00 48 ba - preset 2 red (foreground)
     * b0 b1 b2 b3 00 01 01 67 00 0d 41 02 ff 00 00 ff 00 00 06 00 00 00 47 ca - preset 2 red (foreground), red (background)
     * b0 b1 b2 b3 00 01 01 67 00 0d 41 02 ff 00 00 ff 00 00 06 00 00 00 47 ca - preset 2 red (foreground), red (background)
     * b0 b1 b2 b3 00 01 01 69 00 0d 41 02 ff 00 00 ff 00 00 06 00 00 00 47 cc - preset 2 red (foreground), red (background)
     * b0 b1 b2 b3 00 01 01 6a 00 0d 41 02 ff 00 00 00 ff 00 06 00 00 00 47 cd - preset 2 red (foreground), green (background)
     * b0 b1 b2 b3 00 01 01 77 00 0d 41 02 ff 00 00 00 ff 00 06 00 00 00 47 da - preset 2 red (foreground), green (background) - direction RTL
     * b0 b1 b2 b3 00 01 01 7d 00 0d 41 02 ff 00 00 00 ff 00 06 00 00 00 47 e0 - preset 2 red (foreground), green (background) - direction RTL
     * b0 b1 b2 b3 00 01 01 7d 00 0d 41 02 ff 00 00 00 ff 00 06 00 00 00 47 e0 - preset 2 red (foreground), green (background) - direction RTL
     * b0 b1 b2 b3 00 01 01 7c 00 0d 41 02 ff 00 00 00 ff 00 06 01 00 00 48 e1 - preset 2 red (foreground), green (background) - direction LTR
     * b0 b1 b2 b3 00 01 01 89 00 0d 41 02 ff 00 00 00 ff 00 00 00 00 00 41 e0 - preset 2 red (foreground), green (background) - direction LTR - speed 0
     * b0 b1 b2 b3 00 01 01 8a 00 0d 41 02 ff 00 00 00 ff 00 64 00 00 00 a5 a9 - preset 2 red (foreground), green (background) - direction LTR - speed 64
     * b0 b1 b2 b3 00 01 01 8b 00 0d 41 02 ff 00 00 00 ff 00 00 00 00 00 41 e2 - preset 2 red (foreground), green (background) - direction LTR - speed 0?
     * b0 b1 b2 b3 00 01 01 8c 00 0d 41 02 ff 00 00 00 ff 00 64 00 00 00 a5 ab - preset 2 red (foreground), green (background) - direction LTR - speed 64?
     *
     * Set Blue
     * b0b1b2b30001010b0034a0000600010000ff0000ff0002ffff000000ff00030000ff0000ff0004ffff000000ff00050000ff0000ff0006ffff000000ffac5f
     *
     * Query
     * b0b1b2b30001010c0004818a8b9604
     * b0b1b2b30001010c000e811a23280000640f000001000660a2
     *
     * Set Red
     * b0b1b2b30001010d0034a0000600010000ff0000ff0002ff00000000ff00030000ff0000ff0004ff00000000ff00050000ff0000ff0006ff00000000ffaf67
     *
     * @param bool $persist     Whether to persist the changes.
     * @param int  $red         Red value (0-255).
     * @param int  $green       Green value (0-255).
     * @param int  $blue        Blue value (0-255).
     * @param int  $warmwhite   Warm white value (0-255).
     * @param int  $coolwhite   Cool white value (0-255).
     * @param int  $writemode   Write mode / color mask.
     *
     * @return int[] Message bytes.
     */
    public function ConstructLevelsChange(bool $persist, int $red, int $green, int $blue, int $warmwhite, int $coolwhite, int $writemode): array
    {
        $counter_byte = $this->IncrementCounter();
        $preset_number = 0x01;  # aka fixed color
        $msg = [0x41, $preset_number, $red, $green, $blue, 0x00, 0x00, 0x00, 0x06, 0x01, 0x00, 0x00];
        $inner_message = $this->ConstructMessage($msg);
        $msg = array_merge($this->ADDRESSABLE_HEADER, [$counter_byte, 0x00, 0x0D]);
        $msg = array_merge($msg, $inner_message);
        return $this->ConstructMessage($msg);
    }

    /**
     * The bytes to send for a preset pattern.
     *
     * @param int $pattern    Pattern number.
     * @param int $speed      Speed (0-100).
     * @param int $brightness Brightness (0-255).
     *
     * @return int[] Message bytes.
     */
    public function ConstructPresetPattern(int $pattern, int $speed, int $brightness): array
    {
        $counter_byte = $this->IncrementCounter();
        $msg = array_merge($this->ADDRESSABLE_HEADER, [$counter_byte, 0x00, 0x05, 0x42, $pattern, $speed, $brightness, 0x00]);
        return $this->ConstructMessage($msg);
    }
}

/**
 * The newer LEDENET protocol CCT (color temperature only, no RGB).
 */
class ProtocolLEDENETCCT extends ProtocolLEDENET9Byte
{
    /** Minimum brightness value accepted by the device. */
    private int $MIN_BRIGHTNESS = 2;

    /**
     * The id of the protocol.
     *
     * @return int ID of the protocol.
     */
    public function Id(): int
    {
        return self::PROTOCOL_LEDENET_CCT;
    }

    /**
     * Protocol supports dimmable effects.
     *
     * @return bool True if dimmable effects are supported, false otherwise.
     */
    public function DimmableEffects(): bool
    {
        return false;
    }

    /**
     * The bytes to send for a level change request.
     *
     * b0 b1 b2 b3 00 01 01 52 00 09 35 b1 00 64 00 00 00 03 4d bd - 100% warm
     * b0 b1 b2 b3 00 01 01 72 00 09 35 b1 64 64 00 00 00 03 b1 a5 - 100% cool
     * b0 b1 b2 b3 00 01 01 9f 00 09 35 b1 64 32 00 00 00 03 7f 6e - 100% cool - dim 50%
     *
     * @param bool $persist     Whether to persist the changes.
     * @param int  $red         Red value (0-255).
     * @param int  $green       Green value (0-255).
     * @param int  $blue        Blue value (0-255).
     * @param int  $warmwhite   Warm white value (0-255).
     * @param int  $coolwhite   Cool white value (0-255).
     *
     * @return int[] Message bytes.
     */
    public function ConstructLevelsChange(bool $persist, int $red, int $green, int $blue, int $warmwhite, int $coolwhite, int $writemode): array
    {
        $counter_byte = $this->IncrementCounter();
        $tb = $this->WhiteLevelsToScaledColorTemp($warmwhite, $coolwhite);
        $scaled_temp = $tb[0];
        $brightness = $tb[1];
        # If the brightness goes below the precision the device
        # will flip from cold to warm
        $msg = [0x35, 0xB1, $scaled_temp, max($this->MIN_BRIGHTNESS, $brightness), 0x00, 0x00, 0x00, 0x03];
        $inner_message = $this->ConstructMessage($msg);
        $msg = array_merge($this->ADDRESSABLE_HEADER, [$counter_byte, 0x00, 0x09]);
        $msg = array_merge($msg, $inner_message);
        return $this->ConstructMessage($msg);
    }
}