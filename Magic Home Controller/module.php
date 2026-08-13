<?php

declare(strict_types=1);

/** Generell funktions  */
require_once __DIR__ . '/../libs/_traits.php';

/** Namespaced traits */
use Wilkware\MagicHomeController\DebugHelper;
use Wilkware\MagicHomeController\MagicHelper;
use Wilkware\MagicHomeController\ProtocolLEDENET8Byte;

use Wilkware\MagicHomeController\ProtocolLEDENET8ByteDimmableEffects;
use Wilkware\MagicHomeController\ProtocolLEDENET9Byte;
use Wilkware\MagicHomeController\ProtocolLEDENET9ByteDimmableEffects;
use Wilkware\MagicHomeController\ProtocolLEDENETAddressableA1;
use Wilkware\MagicHomeController\ProtocolLEDENETAddressableA2;
use Wilkware\MagicHomeController\ProtocolLEDENETAddressableA3;
use Wilkware\MagicHomeController\ProtocolLEDENETCCT;
use Wilkware\MagicHomeController\ProtocolLEDENETOriginal;
use Wilkware\MagicHomeController\VariableHelper;

/**
 * CLASS MagicHomeController
 */
class MagicHomeController extends IPSModuleStrict
{
    // -------------------------------------------------------------------------
    // Traits
    // -------------------------------------------------------------------------

    use DebugHelper;
    use MagicHelper;
    use VariableHelper;

    // -------------------------------------------------------------------------
    // Socket Constants
    // -------------------------------------------------------------------------

    /** @var int Socket Port */
    private const SOCKET_PORT = 5577;

    /** @var int Socket Time */
    private const SOCKET_TIME = 2;

    // -------------------------------------------------------------------------
    // Presentations
    // -------------------------------------------------------------------------

    /**
     * @var array<string,mixed> Switch Presentation (Switch)
     */
    private const MHC_PRESENTATION_SWITCH = [
        'PRESENTATION'   => VARIABLE_PRESENTATION_SWITCH,
        'USE_ICON_FALSE' => true,
        'USAGE_TYPE'     => 0,
        'ICON_TRUE'      => 'lightbulb-on',
        'ICON_FALSE'     => 'lightbulb',
        'GLOW_INTENSITY' => 50,
        'GLOW_COLOR'     => 16771899,
    ];

    /**
     * @var array<string,mixed> HexColor Presentation (Color)
     */
    private const MHC_PRESENTATION_COLOR = [
        'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,
        'USAGE_TYPE'   => 2,
        'PERCENTAGE'   => true,
        'ICON'         => 'signal',
        'STEP_SIZE'    => 1.0,
        'SUFFIX'       => ' %',
    ];

    /**
     * @var array<string,mixed> Intensity Presentation (Slider)
     */
    private const MHC_PRESENTATION_SLIDER = [
        'PRESENTATION' => VARIABLE_PRESENTATION_SLIDER,

    ];

    /**
     * @var array<string,mixed> Preset Presentation (Enumeration)
     */
    private const MHC_PRESENTATION_PRESET = [
        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
        'OPTIONS'      => '[{"Caption":"Manually","Color":-1,"IconActive":false,"IconValue":"","Value":0},{"Caption":"7-step color sequence","Color":-1,"IconActive":false,"IconValue":"","Value":37},{"Caption":"pulsing red","Color":16711680,"IconActive":false,"IconValue":"","Value":38},{"Caption":"pulsing green","Color":65280,"IconActive":false,"IconValue":"","Value":39},{"Caption":"pulsing blue","Color":255,"IconActive":false,"IconValue":"","Value":40},{"Caption":"pulsing yellow","Color":16776960,"IconActive":false,"IconValue":"","Value":41},{"Caption":"pulsing cyan","Color":65535,"IconActive":false,"IconValue":"","Value":42},{"Caption":"pulsing purple","Color":16711935,"IconActive":false,"IconValue":"","Value":43},{"Caption":"pulsing white","Color":16777215,"IconActive":false,"IconValue":"","Value":44},{"Caption":"pulsing red + green","Color":15790080,"IconActive":false,"IconValue":"","Value":45},{"Caption":"pulsing red + blue","Color":15728880,"IconActive":false,"IconValue":"","Value":46},{"Caption":"ulsing green + blue","Color":61680,"IconActive":false,"IconValue":"","Value":47},{"Caption":"7-step flashing","Color":10526880,"IconActive":false,"IconValue":"","Value":48},{"Caption":"flashing red","Color":16711680,"IconActive":false,"IconValue":"","Value":49},{"Caption":"flashing green","Color":65280,"IconActive":false,"IconValue":"","Value":50},{"Caption":"flashing blue","Color":255,"IconActive":false,"IconValue":"","Value":51},{"Caption":"flashing yellow","Color":16776960,"IconActive":false,"IconValue":"","Value":52},{"Caption":"flashing cyan","Color":65535,"IconActive":false,"IconValue":"","Value":53},{"Caption":"flashing purple","Color":16711935,"IconActive":false,"IconValue":"","Value":54},{"Caption":"flashing white","Color":16777215,"IconActive":false,"IconValue":"","Value":55},{"Caption":"7-step color jumping","Color":-1,"IconActive":false,"IconValue":"","Value":56}]',
        'LAYOUT'       => 0,
        'ICON'         => 'Bulb',
        'DISPLAY'      => 0,
    ];

    /**
     * @var array<string,mixed> Original Presentation (Enumeration)
     */
    private const MHC_PRESENTATION_ORIGINAL = [
        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
        'OPTIONS'      => '[{"Caption":"Manually","Color":-1,"IconActive":false,"IconValue":"","Value":0},{"Caption":"Circulate all modes","Color":-1,"IconActive":false,"IconValue":"","Value":1},{"Caption":"7 colors change gradually","Color":-1,"IconActive":false,"IconValue":"","Value":2},{"Caption":"7 colors run in olivary","Color":-1,"IconActive":false,"IconValue":"","Value":3},{"Caption":"7 colors change quickly","Color":-1,"IconActive":false,"IconValue":"","Value":4},{"Caption":"7 colors strobe-flash","Color":-1,"IconActive":false,"IconValue":"","Value":5},{"Caption":"7 colors running, 1 point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":6},{"Caption":"7 colors running, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":7},{"Caption":"7 colors overlay, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":8},{"Caption":"7 colors overlay, multi points from the middle to the both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":9},{"Caption":"7 colors flow gradually, from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":10},{"Caption":"Fading out run, 7 colors from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":11},{"Caption":"Runs in olivary, 7 colors from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":12},{"Caption":"Fading out run, 7 colors start with white color from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":13},{"Caption":"Run circularly, 7 colors with black background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":14},{"Caption":"Run circularly, 7 colors with red background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":15},{"Caption":"Run circularly, 7 colors with green background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":16},{"Caption":"Run circularly, 7 colors with blue background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":17},{"Caption":"Run circularly, 7 colors with yellow background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":18},{"Caption":"Run circularly, 7 colors with purple background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":19},{"Caption":"Run circularly, 7 colors with cyan background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":20},{"Caption":"Run circularly, 7 colors with white background, 1point from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":21},{"Caption":"Run circularly, 7 colors with black background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":22},{"Caption":"Run circularly, 7 colors with red background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":23},{"Caption":"Run circularly, 7 colors with green background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":24},{"Caption":"Run circularly, 7 colors with blue background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":25},{"Caption":"Run circularly, 7 colors with yellow background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":26},{"Caption":"Run circularly, 7 colors with purple background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":27},{"Caption":"Run circularly, 7 colors with cyan background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":28},{"Caption":"Run circularly, 7 colors with white background, 1point from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":29},{"Caption":"Run circularly, 7 colors with black background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":30},{"Caption":"Run circularly, 7 colors with red background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":31},{"Caption":"Run circularly, 7 colors with green background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":32},{"Caption":"Run circularly, 7 colors with blue background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":33},{"Caption":"Run circularly, 7 colors with yellow background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":34},{"Caption":"Run circularly, 7 colors with purple background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":35},{"Caption":"Run circularly, 7 colors with cyan background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":36},{"Caption":"Run circularly, 7 colors with white background, 1point from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":37},{"Caption":"Run circularly, 7 colors with black background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":38},{"Caption":"Run circularly, 7 colors with red background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":39},{"Caption":"Run circularly, 7 colors with green background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":40},{"Caption":"Run circularly, 7 colors with blue background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":41},{"Caption":"Run circularly, 7 colors with yellow background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":42},{"Caption":"Run circularly, 7 colors with purple background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":43},{"Caption":"Run circularly, 7 colors with cyan background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":44},{"Caption":"Run circularly, 7 colors with white background, 1point from middle to both ends","Color":-1,"IconActive":false,"IconValue":"","Value":45},{"Caption":"Run circularly, 7 colors with black background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":46},{"Caption":"Run circularly, 7 colors with red background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":47},{"Caption":"Run circularly, 7 colors with green background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":48},{"Caption":"Run circularly, 7 colors with blue background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":49},{"Caption":"Run circularly, 7 colors with yellow background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":50},{"Caption":"Run circularly, 7 colors with purple background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":51},{"Caption":"Run circularly, 7 colors with cyan background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":52},{"Caption":"Run circularly, 7 colors with white background, 1point from both ends to middle","Color":-1,"IconActive":false,"IconValue":"","Value":53},{"Caption":"Run circularly, 7 colors with black background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":54},{"Caption":"Run circularly, 7 colors with red background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":55},{"Caption":"Run circularly, 7 colors with green background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":56},{"Caption":"Run circularly, 7 colors with blue background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":57},{"Caption":"Run circularly, 7 colors with yellow background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":58},{"Caption":"Run circularly, 7 colors with purple background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":59},{"Caption":"Run circularly, 7 colors with cyan background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":60},{"Caption":"Run circularly, 7 colors with white background, 1point from middle to both ends and return back","Color":-1,"IconActive":false,"IconValue":"","Value":61},{"Caption":"Fading out run circularly, 7 colors each in red fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":203},{"Caption":"Fading out run circularly, 7 colors each in green fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":204},{"Caption":"Fading out run circularly, 7 colors each in blue fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":205},{"Caption":"Fading out run circularly, 7 colors each in yellow fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":206},{"Caption":"Fading out run circularly, 7 colors each in purple fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":207},{"Caption":"Fading out run circularly, 7 colors each in cyan fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":208},{"Caption":"Fading out run circularly, 7 colors each in white fading from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":209},{"Caption":"Fading out run circularly, 7 colors each in red fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":210},{"Caption":"Fading out run circularly, 7 colors each in green fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":211},{"Caption":"Fading out run circularly, 7 colors each in blue fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":212},{"Caption":"Fading out run circularly, 7 colors each in yellow fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":213},{"Caption":"Fading out run circularly, 7 colors each in purple fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":214},{"Caption":"Fading out run circularly, 7 colors each in cyan fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":215},{"Caption":"Fading out run circularly, 7 colors each in white fading from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":216},{"Caption":"Fading out run circularly, 7 colors each in red fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":217},{"Caption":"Fading out run circularly, 7 colors each in green fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":218},{"Caption":"Fading out run circularly, 7 colors each in blue fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":219},{"Caption":"Fading out run circularly, 7 colors each in yellow fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":220},{"Caption":"Fading out run circularly, 7 colors each in purple fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":221},{"Caption":"Fading out run circularly, 7 colors each in cyan fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":222},{"Caption":"Fading out run circularly, 7 colors each in white fading from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":223},{"Caption":"7 colors each in red run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":224},{"Caption":"7 colors each in green run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":225},{"Caption":"7 colors each in blue run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":226},{"Caption":"7 colors each in yellow run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":227},{"Caption":"7 colors each in purple run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":228},{"Caption":"7 colors each in cyan run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":229},{"Caption":"7 colors each in white run circularly, multi points from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":230},{"Caption":"7 colors each in red run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":231},{"Caption":"7 colors each in green run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":232},{"Caption":"7 colors each in blue run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":233},{"Caption":"7 colors each in yellow run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":234},{"Caption":"7 colors each in purple run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":235},{"Caption":"7 colors each in cyan run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":236},{"Caption":"7 colors each in white run circularly, multi points from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":237},{"Caption":"7 colors each in red run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":238},{"Caption":"7 colors each in green run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":239},{"Caption":"7 colors each in blue run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":240},{"Caption":"7 colors each in yellow run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":241},{"Caption":"7 colors each in purple run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":242},{"Caption":"7 colors each in cyan run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":243},{"Caption":"7 colors each in white run circularly, multi points from start to end and return back","Color":-1,"IconActive":false,"IconValue":"","Value":244},{"Caption":"7 colors run with black background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":266},{"Caption":"7 colors run with red background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":267},{"Caption":"7 colors run with green background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":268},{"Caption":"7 colors run with blue background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":269},{"Caption":"7 colors run with yellow background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":270},{"Caption":"7 colors run with purple background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":271},{"Caption":"7 colors run with cyan background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":272},{"Caption":"7 colors run with white background from start to end","Color":-1,"IconActive":false,"IconValue":"","Value":273},{"Caption":"7 colors run with black background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":274},{"Caption":"7 colors run with red background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":275},{"Caption":"7 colors run with green background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":276},{"Caption":"7 colors run with blue background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":277},{"Caption":"7 colors run with yellow background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":278},{"Caption":"7 colors run with purple background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":279},{"Caption":"7 colors run with cyan background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":280},{"Caption":"7 colors run with white background from end to start","Color":-1,"IconActive":false,"IconValue":"","Value":281},{"Caption":"7 colors run gradually + 7 colors change quickly","Color":-1,"IconActive":false,"IconValue":"","Value":291},{"Caption":"7 colors run gradually + 7 colors flash","Color":-1,"IconActive":false,"IconValue":"","Value":292},{"Caption":"7 colors change quickly + 7 colors flash","Color":-1,"IconActive":false,"IconValue":"","Value":295},{"Caption":"7 colors run gradually + 7 colors change quickly + 7 colors flash","Color":-1,"IconActive":false,"IconValue":"","Value":298},{"Caption":"7 colors run in olivary + 7 colors change quickly + 7 colors flash","Color":-1,"IconActive":false,"IconValue":"","Value":299},{"Caption":"7 colors run gradually + 7 colors run in olivary + 7 colors change quickly + 7 color flash","Color":-1,"IconActive":false,"IconValue":"","Value":300}]',
        'LAYOUT'       => 0,
        'ICON'         => 'Bulb',
        'DISPLAY'      => 0,
    ];

    /**
     * @var array<string,mixed> Addressable Presentation (Enumeration)
     */
    private const MHC_PRESENTATION_ADDRESSABLE = [
        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
        'OPTIONS'      => '[{"Caption":"Manually","Color":-1,"IconActive":false,"IconValue":"","Value":0},{"Caption":"RBM 1","Color":-1,"IconActive":false,"IconValue":"","Value":1},{"Caption":"RBM 2","Color":-1,"IconActive":false,"IconValue":"","Value":2},{"Caption":"RBM 3","Color":-1,"IconActive":false,"IconValue":"","Value":3},{"Caption":"RBM 4","Color":-1,"IconActive":false,"IconValue":"","Value":4},{"Caption":"RBM 5","Color":-1,"IconActive":false,"IconValue":"","Value":5},{"Caption":"RBM 6","Color":-1,"IconActive":false,"IconValue":"","Value":6},{"Caption":"RBM 7","Color":-1,"IconActive":false,"IconValue":"","Value":7},{"Caption":"RBM 8","Color":-1,"IconActive":false,"IconValue":"","Value":8},{"Caption":"RBM 9","Color":-1,"IconActive":false,"IconValue":"","Value":9},{"Caption":"RBM 10","Color":-1,"IconActive":false,"IconValue":"","Value":10},{"Caption":"RBM 11","Color":-1,"IconActive":false,"IconValue":"","Value":11},{"Caption":"RBM 12","Color":-1,"IconActive":false,"IconValue":"","Value":12},{"Caption":"RBM 13","Color":-1,"IconActive":false,"IconValue":"","Value":13},{"Caption":"RBM 14","Color":-1,"IconActive":false,"IconValue":"","Value":14},{"Caption":"RBM 15","Color":-1,"IconActive":false,"IconValue":"","Value":15},{"Caption":"RBM 16","Color":-1,"IconActive":false,"IconValue":"","Value":16},{"Caption":"RBM 17","Color":-1,"IconActive":false,"IconValue":"","Value":17},{"Caption":"RBM 18","Color":-1,"IconActive":false,"IconValue":"","Value":18},{"Caption":"RBM 19","Color":-1,"IconActive":false,"IconValue":"","Value":19},{"Caption":"RBM 20","Color":-1,"IconActive":false,"IconValue":"","Value":20},{"Caption":"RBM 21","Color":-1,"IconActive":false,"IconValue":"","Value":21},{"Caption":"RBM 22","Color":-1,"IconActive":false,"IconValue":"","Value":22},{"Caption":"RBM 23","Color":-1,"IconActive":false,"IconValue":"","Value":23},{"Caption":"RBM 24","Color":-1,"IconActive":false,"IconValue":"","Value":24},{"Caption":"RBM 25","Color":-1,"IconActive":false,"IconValue":"","Value":25},{"Caption":"RBM 26","Color":-1,"IconActive":false,"IconValue":"","Value":26},{"Caption":"RBM 27","Color":-1,"IconActive":false,"IconValue":"","Value":27},{"Caption":"RBM 28","Color":-1,"IconActive":false,"IconValue":"","Value":28},{"Caption":"RBM 29","Color":-1,"IconActive":false,"IconValue":"","Value":29},{"Caption":"RBM 30","Color":-1,"IconActive":false,"IconValue":"","Value":30},{"Caption":"RBM 31","Color":-1,"IconActive":false,"IconValue":"","Value":31},{"Caption":"RBM 32","Color":-1,"IconActive":false,"IconValue":"","Value":32},{"Caption":"RBM 33","Color":-1,"IconActive":false,"IconValue":"","Value":33},{"Caption":"RBM 34","Color":-1,"IconActive":false,"IconValue":"","Value":34},{"Caption":"RBM 35","Color":-1,"IconActive":false,"IconValue":"","Value":35},{"Caption":"RBM 36","Color":-1,"IconActive":false,"IconValue":"","Value":36},{"Caption":"RBM 37","Color":-1,"IconActive":false,"IconValue":"","Value":37},{"Caption":"RBM 38","Color":-1,"IconActive":false,"IconValue":"","Value":38},{"Caption":"RBM 39","Color":-1,"IconActive":false,"IconValue":"","Value":39},{"Caption":"RBM 40","Color":-1,"IconActive":false,"IconValue":"","Value":40},{"Caption":"RBM 41","Color":-1,"IconActive":false,"IconValue":"","Value":41},{"Caption":"RBM 42","Color":-1,"IconActive":false,"IconValue":"","Value":42},{"Caption":"RBM 43","Color":-1,"IconActive":false,"IconValue":"","Value":43},{"Caption":"RBM 44","Color":-1,"IconActive":false,"IconValue":"","Value":44},{"Caption":"RBM 45","Color":-1,"IconActive":false,"IconValue":"","Value":45},{"Caption":"RBM 46","Color":-1,"IconActive":false,"IconValue":"","Value":46},{"Caption":"RBM 47","Color":-1,"IconActive":false,"IconValue":"","Value":47},{"Caption":"RBM 48","Color":-1,"IconActive":false,"IconValue":"","Value":48},{"Caption":"RBM 49","Color":-1,"IconActive":false,"IconValue":"","Value":49},{"Caption":"RBM 50","Color":-1,"IconActive":false,"IconValue":"","Value":50},{"Caption":"RBM 51","Color":-1,"IconActive":false,"IconValue":"","Value":51},{"Caption":"RBM 52","Color":-1,"IconActive":false,"IconValue":"","Value":52},{"Caption":"RBM 53","Color":-1,"IconActive":false,"IconValue":"","Value":53},{"Caption":"RBM 54","Color":-1,"IconActive":false,"IconValue":"","Value":54},{"Caption":"RBM 55","Color":-1,"IconActive":false,"IconValue":"","Value":55},{"Caption":"RBM 56","Color":-1,"IconActive":false,"IconValue":"","Value":56},{"Caption":"RBM 57","Color":-1,"IconActive":false,"IconValue":"","Value":57},{"Caption":"RBM 58","Color":-1,"IconActive":false,"IconValue":"","Value":58},{"Caption":"RBM 59","Color":-1,"IconActive":false,"IconValue":"","Value":59},{"Caption":"RBM 60","Color":-1,"IconActive":false,"IconValue":"","Value":60},{"Caption":"RBM 61","Color":-1,"IconActive":false,"IconValue":"","Value":61},{"Caption":"RBM 62","Color":-1,"IconActive":false,"IconValue":"","Value":62},{"Caption":"RBM 63","Color":-1,"IconActive":false,"IconValue":"","Value":63},{"Caption":"RBM 64","Color":-1,"IconActive":false,"IconValue":"","Value":64},{"Caption":"RBM 65","Color":-1,"IconActive":false,"IconValue":"","Value":65},{"Caption":"RBM 66","Color":-1,"IconActive":false,"IconValue":"","Value":66},{"Caption":"RBM 67","Color":-1,"IconActive":false,"IconValue":"","Value":67},{"Caption":"RBM 68","Color":-1,"IconActive":false,"IconValue":"","Value":68},{"Caption":"RBM 69","Color":-1,"IconActive":false,"IconValue":"","Value":69},{"Caption":"RBM 70","Color":-1,"IconActive":false,"IconValue":"","Value":70},{"Caption":"RBM 71","Color":-1,"IconActive":false,"IconValue":"","Value":71},{"Caption":"RBM 72","Color":-1,"IconActive":false,"IconValue":"","Value":72},{"Caption":"RBM 73","Color":-1,"IconActive":false,"IconValue":"","Value":73},{"Caption":"RBM 74","Color":-1,"IconActive":false,"IconValue":"","Value":74},{"Caption":"RBM 75","Color":-1,"IconActive":false,"IconValue":"","Value":75},{"Caption":"RBM 76","Color":-1,"IconActive":false,"IconValue":"","Value":76},{"Caption":"RBM 77","Color":-1,"IconActive":false,"IconValue":"","Value":77},{"Caption":"RBM 78","Color":-1,"IconActive":false,"IconValue":"","Value":78},{"Caption":"RBM 79","Color":-1,"IconActive":false,"IconValue":"","Value":79},{"Caption":"RBM 80","Color":-1,"IconActive":false,"IconValue":"","Value":80},{"Caption":"RBM 81","Color":-1,"IconActive":false,"IconValue":"","Value":81},{"Caption":"RBM 82","Color":-1,"IconActive":false,"IconValue":"","Value":82},{"Caption":"RBM 83","Color":-1,"IconActive":false,"IconValue":"","Value":83},{"Caption":"RBM 84","Color":-1,"IconActive":false,"IconValue":"","Value":84},{"Caption":"RBM 85","Color":-1,"IconActive":false,"IconValue":"","Value":85},{"Caption":"RBM 86","Color":-1,"IconActive":false,"IconValue":"","Value":86},{"Caption":"RBM 87","Color":-1,"IconActive":false,"IconValue":"","Value":87},{"Caption":"RBM 88","Color":-1,"IconActive":false,"IconValue":"","Value":88},{"Caption":"RBM 89","Color":-1,"IconActive":false,"IconValue":"","Value":89},{"Caption":"RBM 90","Color":-1,"IconActive":false,"IconValue":"","Value":90},{"Caption":"RBM 91","Color":-1,"IconActive":false,"IconValue":"","Value":91},{"Caption":"RBM 92","Color":-1,"IconActive":false,"IconValue":"","Value":92},{"Caption":"RBM 93","Color":-1,"IconActive":false,"IconValue":"","Value":93},{"Caption":"RBM 94","Color":-1,"IconActive":false,"IconValue":"","Value":94},{"Caption":"RBM 95","Color":-1,"IconActive":false,"IconValue":"","Value":95},{"Caption":"RBM 96","Color":-1,"IconActive":false,"IconValue":"","Value":96},{"Caption":"RBM 97","Color":-1,"IconActive":false,"IconValue":"","Value":97},{"Caption":"RBM 98","Color":-1,"IconActive":false,"IconValue":"","Value":98},{"Caption":"RBM 99","Color":-1,"IconActive":false,"IconValue":"","Value":99},{"Caption":"RBM 100","Color":-1,"IconActive":false,"IconValue":"","Value":100},{"Caption":"RBM 101","Color":-1,"IconActive":false,"IconValue":"","Value":101},{"Caption":"RBM 102","Color":-1,"IconActive":false,"IconValue":"","Value":102},{"Caption":"Circulate all modes","Color":-1,"IconActive":false,"IconValue":"","Value":255}]',
        'LAYOUT'       => 0,
        'ICON'         => 'Bulb',
        'DISPLAY'      => 0,
    ];

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
        // Never delete this line!
        parent::Create();

        // Device Variablen
        $this->RegisterPropertyInteger('TYPE', 51);
        $this->RegisterPropertyString('MODEL', 'Unknown');
        $this->RegisterPropertyString('TCPIP', '127.0.0.1');
        $this->RegisterPropertyString('MAC', '');
        $this->RegisterPropertyString('RGB', '012');

        // Setup Presentations
        $preset = $this->TranslatePresentation(self::MHC_PRESENTATION_PRESET, 'OPTIONS', 'Caption');
        $original = $this->TranslatePresentation(self::MHC_PRESENTATION_ORIGINAL, 'OPTIONS', 'Caption');
        $addressable = $this->TranslatePresentation(self::MHC_PRESENTATION_ADDRESSABLE, 'OPTIONS', 'Caption');

        // Variablen erzeugen
        $varID = $this->RegisterVariableBoolean('Power', $this->Translate('Power'), self::MHC_PRESENTATION_SWITCH, 0);
        $this->EnableAction('Power');
        $varID = $this->RegisterVariableInteger('Color', $this->Translate('Color'), self::MHC_PRESENTATION_COLOR, 1);
        $this->EnableAction('Color');
        $varID = $this->RegisterVariableInteger('Speed', $this->Translate('Speed'), self::MHC_PRESENTATION_SLIDER, 2);
        $this->EnableAction('Speed');
        $varID = $this->RegisterVariableInteger('Brightness', $this->Translate('Brightness'), self::MHC_PRESENTATION_SLIDER, 3);
        $this->EnableAction('Brightness');
        $varID = $this->RegisterVariableInteger('Mode', $this->Translate('Mode'), $preset, 4);
        $this->EnableAction('Mode');
    }

    /**
     * This function is called when deleting the instance during operation and when updating via "Module Control".
     * The function is not called when exiting IP-Symcon.
     *
     * @return void
     */
    public function Destroy(): void
    {
        // Never delete this line!
        parent::Destroy();
    }

    /**
     * Is executed when "Apply" is pressed on the configuration page and immediately after the instance has been created.
     *
     * @return void
     */
    public function ApplyChanges(): void
    {
        // Never delete this line!
        parent::ApplyChanges();

        // Values
        $type = $this->ReadPropertyInteger('TYPE');
        $tcpip = $this->ReadPropertyString('TCPIP');
        $rgb = $this->ReadPropertyString('RGB');

        // IP Check
        if (filter_var($tcpip, FILTER_VALIDATE_IP) !== false) {
            $this->SetStatus(102);
        } else {
            $this->SetStatus(201);
        }

        // Setup variable profil
        $this->RegisterVariableInteger('Mode', $this->Translate('Mode'), $this->GetPatternProfile($type), 4);

        // Debug message
        $this->LogDebug(__FUNCTION__, 'TYPE=0x' . dechex($type) . ', IP=' . $tcpip . ', RGB=' . $rgb);
    }

    /**
     * Is called when, for example, a button is clicked in the visualization.
     *
     * @param string $ident Ident of the variable
     * @param mixed $value The value to be set
     * @return void
     */
    public function RequestAction(string $ident, mixed $value): void
    {
        // Debug
        $this->LogDebug(__FUNCTION__, $ident . ' => ' . $value);
        switch ($ident) {
            // Switch Power On/Off
            case 'Power':
                $this->SetValueBoolean($ident, $value);
                $this->SendPower($value);
                break;
                // Set Speed value
            case 'Speed':
                $this->SetValueInteger($ident, $value);
                $this->SendFunction();
                break;
                // Set Display Mode
            case 'Mode':
                $disabled = ($value > 0) ? true : false;
                $this->SetVariableDisabled('Speed', !$disabled);
                $this->SetVariableDisabled('Color', $disabled);
                // Brightness depend on protocol
                $type = $this->ReadPropertyInteger('TYPE');
                $prot = self::MAGIC_HOME_CONTROLLER[$type][1];
                if (in_array($prot, self::BRIGHTNESS_EFFECTS_PROTOCOLS)) {
                    $this->SetVariableDisabled('Brightness', false);
                } else {
                    $this->SetVariableDisabled('Brightness', $disabled);
                }
                $this->SetValueInteger($ident, $value);
                // Manual or Functional mode
                if ($value == 0) {
                    $this->SendColor();
                } else {
                    $this->SendFunction();
                }
                break;
            case 'Color':
            case 'Brightness':
                $this->SetValueInteger($ident, $value);
                $mode = $this->GetValue('Mode');
                // Manual or Functional mode
                if ($mode == 0) {
                    $this->SendColor();
                } else {
                    $this->SendFunction();
                }
                break;
            case 'Syncronize':
                $this->SendSync();
                break;
            default:
                throw new Exception('Invalid Ident');
        }
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetBrightness(int $InstanzID, int $Brightness);
     *
     * @param int $brightness The brightness value to set.
     *
     * @return void
     */
    public function SetBrightness(int $brightness): void
    {
        $this->RequestAction('Brightness', $brightness);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetColor(int $InstanzID, int $Color);
     *
     * @param int $color The color value to set.
     *
     * @return void
     */
    public function SetColor(int $color): void
    {
        $this->RequestAction('Color', $color);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetMode(int $InstanzID, int $Mode);
     *
     * @param int $mode The mode to set.
     *
     *
     */
    public function SetMode(int $mode): void
    {
        $this->RequestAction('Mode', $mode);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetPower(int $InstanzID, bool $Power);
     *
     * @param bool $power The power state to set.
     *
     * @return void
     */
    public function SetPower(bool $power): void
    {
        $this->RequestAction('Power', $power);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * MHC_SetSpeed(int $InstanzID, int $Speed);
     *
     * @param int $speed The speed value to set.
     *
     * @return void
     */
    public function SetSpeed(int $speed): void
    {
        $this->RequestAction('Speed', $speed);
    }

    /**
     * Send power state data.
     *
     * @param bool $value The power state to send.
     *
     * @return void
     */
    private function SendPower(bool $value): void
    {
        $type = $this->ReadPropertyInteger('TYPE');
        $class = self::CLASS_PROTOCOL[self::MAGIC_HOME_CONTROLLER[$type][1]];
        $protocol = new $class();
        $state = $protocol->ConstructStateChange($value);
        $this->SendData($state);
    }

    /**
     * Send function data.
     *
     * @return void
     */
    private function SendFunction(): void
    {
        // Get function code
        $pattern = $this->GetValue('Mode');
        if ($pattern == 0) {
            return; // Only if code not manually
        }
        $speed = $this->GetValue('Speed');
        $brightness = $this->GetValue('Brightness'); // / 100;
        // protocol
        $type = $this->ReadPropertyInteger('TYPE');
        $class = self::CLASS_PROTOCOL[self::MAGIC_HOME_CONTROLLER[$type][1]];
        $protocol = new $class();
        $data = $protocol->ConstructPresetPattern($pattern, $speed, $brightness);
        // send data
        $this->SendData($data);
    }

    /**
     * Send color data.
     *
     * @return void
     */
    private function SendColor(): void
    {
        // Check mode
        $mode = $this->GetValue('Mode');
        if ($mode != 0) {
            return; // Only if code not manually
        }
        $brightness = $this->GetValue('Brightness') / 100;
        $color = $this->GetValue('Color');
        $rgb = [0x00, 0x00, 0x00];
        $rgb[0] = (($color >> 16) & 0xFF); // red
        $rgb[1] = (($color >> 8) & 0xFF); // green
        $rgb[2] = ($color & 0xFF); // blue
        $this->LogDebug(__FUNCTION__, $rgb);
        // map with brightness
        $rgb[0] *= $brightness;
        $rgb[1] *= $brightness;
        $rgb[2] *= $brightness;
        $this->LogDebug(__FUNCTION__, $rgb);
        // map rgb channel
        $channel = $this->ReadPropertyString('RGB');
        $index = (int) $channel[0];
        $this->LogDebug(__FUNCTION__, "0 -> $index");
        $r = floor($rgb[$index]);
        $index = (int) $channel[1];
        $this->LogDebug(__FUNCTION__, "1 -> $index");
        $g = floor($rgb[$index]);
        $index = (int) $channel[2];
        $this->LogDebug(__FUNCTION__, "2 -> $index");
        $b = floor($rgb[$index]);
        // protocol
        $type = $this->ReadPropertyInteger('TYPE');
        $class = self::CLASS_PROTOCOL[self::MAGIC_HOME_CONTROLLER[$type][1]];
        $protocol = new $class();
        $data = $protocol->ConstructLevelsChange(true, (int) $r, (int) $g, (int) $b, 0x00, 0x00, 0x00);
        // send data
        $this->SendData($data);
    }

    /**
     * Sync controller state to variables.
     *
     * @return void
     */
    private function SendSync(): void
    {
        // Send Message ***************************************************************
        $type = $this->ReadPropertyInteger('TYPE');
        $class = self::CLASS_PROTOCOL[self::MAGIC_HOME_CONTROLLER[$type][1]];
        $protocol = new $class();
        $query = $protocol->ConstructStateQuery();
        $data = $this->SendData($query, 2);
        $this->LogDebug(__FUNCTION__, bin2hex($data));
        if (strlen($data) != $protocol->StateResponseLength()) {
            $this->LogDebug(__FUNCTION__, 'No sync possible!');
            return;
        }

        // Convert to array (ONE based)
        $rx = unpack('C*', $data);
        $this->LogDebug(__FUNCTION__, $rx);
        // Check controler type
        if ($rx[2] != $type) {
            $this->LogMessage('Wrong controller type:' . $rx[2], KL_ERROR);
        }

        // Check power state ***********************************************************
        $this->SetValueBoolean('Power', ($rx[3] == 0x23));

        // Check mode ******************************************************************
        $pattern = $rx[4];
        $this->LogDebug(__FUNCTION__, 'Pattern: ' . $pattern);
        // Switches
        if (in_array($protocol->Id(), self::MAGIC_HOME_SWITCHES)) {
            $this->LogDebug(__FUNCTION__, 'Sync for switches not full supported!');
            return;
        }
        // Custom Effects
        if ($pattern == self::EFFECT_CUSTOM_CODE) {
            $this->LogDebug(__FUNCTION__, 'Controler im custom effect mode - not supported!');
            return;
        }
        // Custom Effects
        if ($pattern == self::PRESET_MUSIC_MODE) {
            $this->LogDebug(__FUNCTION__, 'Controler im music mode - not supported!');
            return;
        }
        // Color Mode
        $mode = 0;
        if (in_array($pattern, [0x41, 0x61])) {
            // Manuel
        } elseif (($pattern >= 0x25) && ($pattern <= 0x38)) {
            $mode = $rx[5];
            if (in_array($protocol->Id(), self::ORIGINAL_EFFECTS_PROTOCOLS)) {
                $mode = ($pattern << 8) + $mode - 99;
            } elseif (in_array($protocol->Id(), self::ADDRESSABLE_EFFECTS_PROTOCOLS)) {
                if ($pattern == 0x25) {
                    // mode == $mode
                }
                if ($pattern == 0x24) {
                    $this->LogDebug(__FUNCTION__, 'Controler in multi color effect mode - not supported!');
                    return;
                }
            } else {
                $mode = $pattern;
            }
        } elseif (in_array($protocol->Id(), self::ADDRESSABLE_PROTOCOLS)) {
            $mode = $rx[5];
            if ($mode == 0x61) {
                $mode = 0;
            } else {
                $mode = $mode - 99; // or ($pattern << 8) + $mode - 99;
            }
        }
        $disabled = ($mode > 0) ? true : false;
        $this->SetVariableDisabled('Speed', !$disabled);
        $this->SetVariableDisabled('Color', $disabled);
        if (in_array($protocol->Id(), self::BRIGHTNESS_EFFECTS_PROTOCOLS)) {
            $this->SetVariableDisabled('Brightness', false);
        } else {
            $this->SetVariableDisabled('Brightness', $disabled);
        }
        $this->LogDebug(__FUNCTION__, 'Mode = ' . $mode);
        $this->SetValueInteger('Mode', $mode);

        // Check speed ******************************************************************
        $speed = $rx[6];
        if (!in_array($protocol->Id(), self::ADDRESSABLE_PROTOCOLS)) {
            $speed = $protocol::DelayToSpeed($rx[6]);
        }
        $this->LogDebug(__FUNCTION__, 'Speed = ' . $speed);
        $this->SetValueInteger('Speed', $speed);

        // Check brithness *************************************************************
        $update = false;
        $div = 0;
        if ($mode == 0) {
            // (grössten finden; wenn 0 = weiß mit Helligkeit 0; sonst (max-wert/255) * 100)
            $max = max($rx[7], $rx[8], $rx[9]);
            $this->LogDebug(__FUNCTION__, 'Max = ' . $max);
            $div = $max / 255;
            $this->LogDebug(__FUNCTION__, 'Div = ' . $div);
            $brightness = $div * 100;
            $update = true;
        } elseif (in_array($protocol->Id(), self::BRIGHTNESS_EFFECTS_PROTOCOLS)) {
            // the red byte holds the brightness during an effect
            $brightness = $rx[7];
            $update = true;
        }
        if ($update) {
            $this->LogDebug(__FUNCTION__, 'Brightness = ' . $brightness);
            if ($brightness > 100) {
                $brightness = 100;
            }
            $this->SetValueInteger('Brightness', intval($brightness));
        }
        // Check color *****************************************************************
        if ($mode == 0) {
            $channel = $this->ReadPropertyString('RGB');
            $redChannel = (int) $channel[0];
            $greenChannel = (int) $channel[1];
            $blueChannel = (int) $channel[2];

            $red = $rx[7 + $redChannel] / $div;
            if ($red < 0) {
                $red = 0;
            }
            if ($red > 255) {
                $red = 255;
            }
            $color = intval($red) << 16;
            $green = $rx[7 + $greenChannel] / $div;
            if ($green < 0) {
                $green = 0;
            }
            if ($green > 255) {
                $green = 255;
            }
            $color += intval($green) << 8;
            $blue = $rx[7 + $blueChannel] / $div;
            if ($blue < 0) {
                $blue = 0;
            }
            if ($blue > 255) {
                $blue = 255;
            }
            $color += intval($blue);
            $this->LogDebug(__FUNCTION__, 'Color = 0x' . dechex($red) . dechex($green) . dechex($blue) . ' (' . $color . ')');
            $this->SetValueInteger('Color', $color);
        }
    }

    /**
     * Send data array to controller.
     *
     * @param array<int> $values Configuration Data
     *
     * @return string Received data
     */
    private function SendData(array $values, int $read = 0): string
    {
        $data = '';
        $path = 'tcp://' . $this->ReadPropertyString('TCPIP');
        $socket = @fsockopen($path, self::SOCKET_PORT, $errno, $errstr, self::SOCKET_TIME);
        // Check Socket
        if (!$socket) {
            $this->LogDebug(__FUNCTION__, $path . " -> $errstr ($errno)");
            return $data;
        } else {
            $this->LogDebug(__FUNCTION__, 'Connection etablished: ' . $path);
        }
        $send = '';
        //$this->LogDebug(__FUNCTION__, 'Values=' . print_r($values, true));
        foreach ($values as $value) {
            $send .= chr(intval($value));
        }
        // send data
        fwrite($socket, $send);
        $this->LogDebug(__FUNCTION__, 'Send=' . bin2hex($send));

        // read data
        if ($read != 0) {
            stream_set_timeout($socket, self::SOCKET_TIME);
            while (true) {
                $recv = fread($socket, $read);
                if (($recv === false) || (strlen($recv) != $read)) {
                    break;
                }
                $this->LogDebug(__FUNCTION__, 'Read=' . bin2hex($recv));
                $data .= $recv;
            }
        }

        // close socket
        fclose($socket);

        // return rad data
        return $data;
    }
}
