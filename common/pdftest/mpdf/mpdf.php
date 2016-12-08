<?php	 	

// ******************************************************************************
// Software: mPDF, Unicode-HTML Free PDF generator                              *
// Version:  5.4     based on                                                   *
//           FPDF by Olivier PLATHEY                                            *
//           HTML2FPDF by Renato Coelho                                         *
// Date:     2012-02-14                                                         *
// Author:   Ian Back <ianb@bpm1.com>                                           *
// License:  GPL                                                                *
//                                                                              *
// Changes:  See changelog.txt                                                  *
// ******************************************************************************


define('mPDF_VERSION','5.4');

//Scale factor
define('_MPDFK', (72/25.4));	// Will only use mm

/*-- HTML-CSS --*/
define('AUTOFONT_CJK',1);
define('AUTOFONT_THAIVIET',2);
define('AUTOFONT_RTL',4);
define('AUTOFONT_INDIC',8);
define('AUTOFONT_ALL',15);

define('_BORDER_ALL',15);
define('_BORDER_TOP',8);
define('_BORDER_RIGHT',4);
define('_BORDER_BOTTOM',2);
define('_BORDER_LEFT',1);
/*-- END HTML-CSS --*/

if (!defined('_MPDF_PATH')) define('_MPDF_PATH', dirname(preg_replace('/\\\\/','/',__FILE__)) . '/');
if (!defined('_MPDF_URI')) define('_MPDF_URI',_MPDF_PATH);

require_once(_MPDF_PATH.'includes/functions.php');
require_once(_MPDF_PATH.'config_cp.php');

if (!defined('_JPGRAPH_PATH')) define("_JPGRAPH_PATH", _MPDF_PATH.'jpgraph/'); 

if (!defined('_MPDF_TEMP_PATH')) define("_MPDF_TEMP_PATH", _MPDF_PATH.'tmp/');

if (!defined('_MPDF_TTFONTPATH')) { define('_MPDF_TTFONTPATH',_MPDF_PATH.'ttfonts/'); }
if (!defined('_MPDF_TTFONTDATAPATH')) { define('_MPDF_TTFONTDATAPATH',_MPDF_PATH.'ttfontdata/'); }

$errorlevel=error_reporting();
$errorlevel=error_reporting($errorlevel & ~E_NOTICE);

//error_reporting(E_ALL);

// mPDF 5.3.76
if(function_exists("date_default_timezone_set")) {
	if (ini_get("date.timezone")=="") { date_default_timezone_set("Europe/London"); }
}
if (!function_exists("mb_strlen")) { die("Error - mPDF requires mb_string functions. Ensure that PHP is compiled with php_mbstring.dll enabled."); }

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
// mPDF 5.3.79 Machine dependent number of bytes used to pack "double" into binary (used in cacheTables)
$test = pack("d", 134455.474557333333666);
define("_DSIZE", strlen($test));

class mPDF
{

///////////////////////////////
// EXTERNAL (PUBLIC) VARIABLES
// Define these in config.php
///////////////////////////////
var $cacheTables;		// mPDF 5.3.79
var $bookmarkStyles;	// mPDF 5.3.39
var $useActiveForms;

var $repackageTTF;
var $allowCJKorphans;
var $allowCJKoverflow;
var $CJKleading;
var $CJKfollowing;
var $CJKoverflow;

var $useKerning;
var $restrictColorSpace;
var $bleedMargin;
var $crossMarkMargin;
var $cropMarkMargin;
var $cropMarkLength;
var $nonPrintMargin;

var $PDFX;
var $PDFXauto;

var $PDFA;
var $PDFAauto;
var $ICCProfile;

var $printers_info;
var $iterationCounter;
var $smCapsScale;
var $smCapsStretch;

var $backupSubsFont;
var $backupSIPFont;
var $debugfonts;
var $useAdobeCJK;
var $percentSubset;
var $maxTTFFilesize;
var $BMPonly;

var $tableMinSizePriority;

var $dpi;
var $watermarkImgAlphaBlend;
var $watermarkImgBehind;
var $justifyB4br;
var $packTableData;
var $pgsIns;
var $simpleTables;
var $enableImports;

var $debug;
var $showStats;
var $setAutoTopMargin;
var $setAutoBottomMargin;
var $autoMarginPadding;
var $collapseBlockMargins;
var $falseBoldWeight;
var $normalLineheight;
var $progressBar;
var $incrementFPR1;
var $incrementFPR2;
var $incrementFPR3;
var $incrementFPR4;

var $hyphenate;
var $hyphenateTables;
var $SHYlang;
var $SHYleftmin;
var $SHYrightmin;
var $SHYcharmin;
var $SHYcharmax;
var $SHYlanguages;
// PageNumber Conditional Text
var $pagenumPrefix;
var $pagenumSuffix;
var $nbpgPrefix;
var $nbpgSuffix;
var $showImageErrors;
var $allow_output_buffering;
var $autoPadding;
var $useGraphs;
var $autoFontGroupSize;
var $tabSpaces;
var $useLang;
var $restoreBlockPagebreaks;
var $watermarkTextAlpha;
var $watermarkImageAlpha;
var $watermark_size;
var $watermark_pos;
var $annotSize;
var $annotMargin;
var $annotOpacity;
var $title2annots;
var $keepColumns;
var $keep_table_proportions;
var $ignore_table_widths;
var $ignore_table_percents;
var $list_align_style;
var $list_number_suffix;
var $useSubstitutions;
var $CSSselectMedia;

var $forcePortraitHeaders;
var $forcePortraitMargins;
var $displayDefaultOrientation;
var $ignore_invalid_utf8;
var $allowedCSStags;
var $onlyCoreFonts;
var $allow_charset_conversion;

var $jSWord;
var $jSmaxChar;
var $jSmaxCharLast;
var $jSmaxWordLast;

var $orphansAllowed;
var $max_colH_correction;


var $table_error_report;
var $table_error_report_param;
var $biDirectional;
var $text_input_as_HTML; 
var $anchor2Bookmark;
var $list_indent_first_level;
var $shrink_tables_to_fit;

var $allow_html_optional_endtags;

var $img_dpi;

var $defaultheaderfontsize;
var $defaultheaderfontstyle;
var $defaultheaderline;
var $defaultfooterfontsize;
var $defaultfooterfontstyle;
var $defaultfooterline;
var $header_line_spacing;
var $footer_line_spacing;

var $pregUHCchars;
var $pregSJISchars;
var $pregCJKchars;
var $pregASCIIchars1;
var $pregASCIIchars2;
var $pregASCIIchars3;
var $pregVIETchars;
var $pregVIETPluschars;

var $pregRTLchars;
var $pregHEBchars;
var $pregARABICchars;
var $pregNonARABICchars;
// INDIC
var $pregHIchars;
var $pregBNchars;
var $pregPAchars;
var $pregGUchars;
var $pregORchars;
var $pregTAchars;
var $pregTEchars;
var $pregKNchars;
var $pregMLchars;
var $pregSHchars;
var $pregINDextra;

var $mirrorMargins;
var $default_lineheight_correction;
var $watermarkText;
var $watermarkImage;
var $showWatermarkText;
var $showWatermarkImage;

var $fontsizes;

// Aliases for backward compatability
var $UnvalidatedText;	// alias = $watermarkText
var $TopicIsUnvalidated;	// alias = $showWatermarkText
var $useOddEven;		// alias = $mirrorMargins
var $useSubstitutionsMB;	// alias = $useSubstitutions



//////////////////////
// CLASS OBJECTS
//////////////////////
// mPDF 5.3.89
var $grad;
var $bmp;
var $wmf;
var $tocontents;
var $form;
var $directw;

//////////////////////
// INTERNAL VARIABLES
//////////////////////
	
var $textshadow;	// mPDF 5.3.A2

var $colsums;	// mPDF 5.3.92
// mPDF 5.3.61
var $spanborder;
var $spanborddet;

var $visibility;	// mPDF 5.3.41

var $useRC128encryption;
var $uniqid;

var $kerning;
var $fixedlSpacing;
var $minwSpacing;
var $lSpacingCSS;
var $wSpacingCSS;

var $listDir;
var $spotColorIDs;
var $SVGcolors;
var $spotColors;
var $defTextColor;
var $defDrawColor;
var $defFillColor;

var $tableBackgrounds;
var $inlineDisplayOff;
var $kt_y00;
var $kt_p00;
var $upperCase;
var $checkSIP;
var $checkSMP;
var $checkCJK;
var $tableCJK;

var $watermarkImgAlpha;
var $PDFAXwarnings;
var $MetadataRoot; 
var $OutputIntentRoot;
var $InfoRoot; 
var $current_filename;
var $parsers;
var $current_parser;
var $_obj_stack;
var $_don_obj_stack;
var $_current_obj_id;
var $tpls;
var $tpl;
var $tplprefix;
var $_res;

var $pdf_version;
var $noImageFile;
var $lastblockbottommargin;
var $baselineC;
var $subPos;
var $subArrMB;
var $ReqFontStyle;
var $tableClipPath ;
var $forceExactLineheight;
var $listOcc; 

var $fullImageHeight;
var $inFixedPosBlock;		// Internal flag for position:fixed block
var $fixedPosBlock;		// Buffer string for position:fixed block
var $fixedPosBlockDepth;
var $fixedPosBlockBBox;
var $fixedPosBlockSave;
var $maxPosL;
var $maxPosR;

var $loaded;

var $extraFontSubsets;
var $docTemplateStart;		// Internal flag for page (page no. -1) that docTemplate starts on
var $time0;

// Classes
var $indic;
var $barcode;

var $SHYpatterns;
var $loadedSHYpatterns;
var $loadedSHYdictionary;
var $SHYdictionary;
var $SHYdictionaryWords;

var $spanbgcolorarray;
var $default_font;
var $list_lineheight;
var $headerbuffer;
var $lastblocklevelchange;
var $nestedtablejustfinished;
var $linebreakjustfinished;
var $cell_border_dominance_L;
var $cell_border_dominance_R;
var $cell_border_dominance_T;
var $cell_border_dominance_B;
var $tbCSSlvl;
var $listCSSlvl;
var $table_keep_together;
var $plainCell_properties;
var $inherit_lineheight;
var $listitemtype;
var $shrin_k1;
var $outerfilled;

var $blockContext;
var $floatDivs;

var $tablecascadeCSS;
var $listcascadeCSS;

var $patterns;
var $pageBackgrounds;

var $bodyBackgroundGradient;
var $bodyBackgroundImage;
var $bodyBackgroundColor;

var $writingHTMLheader;	// internal flag - used both for writing HTMLHeaders/Footers and FixedPos block
var $writingHTMLfooter;
var $autoFontGroups;
var $angle;

var $gradients;

var $kwt_Reference;
var $kwt_BMoutlines;
var $kwt_toc;

var $tbrot_Reference;
var $tbrot_BMoutlines;
var $tbrot_toc;

var $col_Reference;
var $col_BMoutlines;
var $col_toc;

var $currentGraphId;
var $graphs;

var $floatbuffer;
var $floatmargins;

var $bullet;
var $bulletarray;

var $rtlAsArabicFarsi;		// DEPRACATED

var $currentLang;
var $default_lang;
var $default_available_fonts;
var $pageTemplate;
var $docTemplate;
var $docTemplateContinue;

var $arabGlyphs;
var $arabHex;
var $persianGlyphs;
var $persianHex;
var $arabVowels;
var $arabPrevLink;
var $arabNextLink;


var $formobjects; // array of Form Objects for WMF
var $InlineProperties;
var $InlineAnnots;
var $ktAnnots;
var $tbrot_Annots;
var $kwt_Annots;
var $columnAnnots;
var $columnForms;

var $PageAnnots;

var $pageDim;	// Keep track of page wxh for orientation changes - set in _beginpage, used in _putannots

var $breakpoints;

var $tableLevel;
var $tbctr;
var $innermostTableLevel;
var $saveTableCounter;
var $cellBorderBuffer;

var $saveHTMLFooter_height;
var $saveHTMLFooterE_height;

var $firstPageBoxHeader;
var $firstPageBoxHeaderEven;
var $firstPageBoxFooter;
var $firstPageBoxFooterEven;

var $page_box;
var $show_marks;	// crop or cross marks

var $basepathIsLocal;

var $use_kwt;
var $kwt;
var $kwt_height;
var $kwt_y0;
var $kwt_x0;
var $kwt_buffer;
var $kwt_Links;
var $kwt_moved;
var $kwt_saved;

var $PageNumSubstitutions;

var $table_borders_separate;
var $base_table_properties;
var $borderstyles;

var $listjustfinished;
var $blockjustfinished;

var $orig_bMargin;
var $orig_tMargin;
var $orig_lMargin;
var $orig_rMargin;
var $orig_hMargin;
var $orig_fMargin;

var $pageheaders;
var $pagefooters;

var $pageHTMLheaders;
var $pageHTMLfooters;

var $saveHTMLHeader;
var $saveHTMLFooter;

var $HTMLheaderPageLinks;
var $HTMLheaderPageAnnots;
var $HTMLheaderPageForms;

// See config_fonts.php for these next 5 values
var $available_unifonts;
var $sans_fonts;
var $serif_fonts;
var $mono_fonts;
var $defaultSubsFont;

// List of ALL available CJK fonts (incl. styles) (Adobe add-ons)  hw removed
var $available_CJK_fonts;

var $cascadeCSS;

var $HTMLHeader;
var $HTMLFooter;
var $HTMLHeaderE;
var $HTMLFooterE;
var $bufferoutput; 

var $showdefaultpagenos;	// DEPRACATED -left for backward compatability


// CJK fonts
var $Big5_widths;
var $GB_widths;
var $SJIS_widths;
var $UHC_widths;

// SetProtection
var $encrypted;	//whether document is protected
var $Uvalue;	//U entry in pdf document
var $Ovalue;	//O entry in pdf document
var $Pvalue;	//P entry in pdf document
var $enc_obj_id;	//encryption object id
var $last_rc4_key;	//last RC4 key encrypted (cached for optimisation)
var $last_rc4_key_c;	//last RC4 computed key
var $encryption_key;
var $padding;	//used for encryption


// Bookmark
var $BMoutlines;
var $OutlineRoot;
// INDEX
var $ColActive;
var $Reference;
var $CurrCol;
var $NbCol;
var $y0;			//Top ordinate of columns
var $ColL;
var $ColWidth;
var $ColGap;
// COLUMNS 
var $ColR;
var $ChangeColumn;
var $columnbuffer;
var $ColDetails;
var $columnLinks;
var $colvAlign;
// Substitutions
var $substitute;		// Array of substitution strings e.g. <ttz>112</ttz>
var $entsearch;		// Array of HTML entities (>ASCII 127) to substitute
var $entsubstitute;	// Array of substitution decimal unicode for the Hi entities


// Default values if no style sheet offered	(cf. http://www.w3.org/TR/CSS21/sample.html)
var $defaultCSS;

var $linemaxfontsize;
var $lineheight_correction;
var $lastoptionaltag;	// Save current block item which HTML specifies optionsl endtag
var $pageoutput;
var $charset_in;
var $blk;
var $blklvl;
var $ColumnAdjust;
var $ws;	// Word spacing
var $HREF;
var $pgwidth;
var $fontlist; 
var $oldx;
var $oldy;
var $B;
var $U;      //underlining flag
var $S;	// SmallCaps flag
var $I;

var $tdbegin;
var $table;
var $cell;
var $col;
var $row;

var $divbegin;
var $divalign;
var $divwidth;
var $divheight;
var $divrevert;
var $spanbgcolor;

var $spanlvl;
var $listlvl;
var $listnum;
var $listtype;
var $listoccur;
var $listlist;
var $listitem;

var $pjustfinished;
var $ignorefollowingspaces;
var $SUP;
var $SUB;
var $SMALL;
var $BIG;
var $toupper;
var $tolower;
var $capitalize;
var $dash_on;
var $dotted_on;
var $strike;

var $CSS;
var $textbuffer;
var $currentfontstyle;
var $currentfontfamily;
var $currentfontsize;
var $colorarray;
var $bgcolorarray;
var $internallink;
var $enabledtags;

var $lineheight;
var $basepath;
var $outlineparam;
var $outline_on;

var $specialcontent;
var $selectoption;
var $objectbuffer;

// Table Rotation
var $table_rotate;
var $tbrot_maxw;
var $tbrot_maxh;
var $tablebuffer;
var $tbrot_align;
var $tbrot_Links;

var $divbuffer;		// Buffer used when keeping DIV on one page
var $keep_block_together;	// Keep a Block from page-break-inside: avoid
var $ktLinks;		// Keep-together Block links array
var $ktBlock;		// Keep-together Block array
var $ktForms;
var $ktReference;
var $ktBMoutlines;
var $_kttoc;

var $tbrot_y0;
var $tbrot_x0;
var $tbrot_w;
var $tbrot_h;

var $mb_enc;
var $directionality;

var $extgstates; // Used for alpha channel - Transparency (Watermark)
var $mgl;
var $mgt;
var $mgr;
var $mgb;

var $tts;
var $ttz;
var $tta;

var $headerDetails;
var $footerDetails;

// Best to alter the below variables using default stylesheet above
var $page_break_after_avoid;
var $margin_bottom_collapse;
var $list_indent;
var $list_align;
var $list_margin_bottom; 
var $default_font_size;	// in pts
var $original_default_font_size;	// used to save default sizes when using table default
var $original_default_font;
var $watermark_font;
var $defaultAlign;

// TABLE
var $defaultTableAlign;
var $tablethead;
var $thead_font_weight;
var $thead_font_style;
var $thead_font_smCaps;
var $thead_valign_default;
var $thead_textalign_default;
var $tabletfoot;
var $tfoot_font_weight;
var $tfoot_font_style;
var $tfoot_font_smCaps;
var $tfoot_valign_default;
var $tfoot_textalign_default;

var $trow_text_rotate;

var $cellPaddingL;
var $cellPaddingR;
var $cellPaddingT;
var $cellPaddingB;
var $table_lineheight;
var $table_border_attr_set;
var $table_border_css_set;

var $shrin_k;			// factor with which to shrink tables - used internally - do not change
var $shrink_this_table_to_fit;	// 0 or false to disable; value (if set) gives maximum factor to reduce fontsize
var $MarginCorrection;	// corrects for OddEven Margins
var $margin_footer;
var $margin_header;

var $tabletheadjustfinished;
var $usingCoreFont;
var $charspacing;

//Private properties FROM FPDF
var $DisplayPreferences; 
var $outlines;
var $flowingBlockAttr;
var $page;               //current page number
var $n;                  //current object number
var $offsets;            //array of object offsets
var $buffer;             //buffer holding in-memory PDF
var $pages;              //array containing pages
var $state;              //current document state
var $compress;           //compression flag
var $DefOrientation;     //default orientation
var $CurOrientation;     //current orientation
var $OrientationChanges; //array indicating orientation changes
var $k;                  //scale factor (number of points in user unit)
var $fwPt;
var $fhPt;         //dimensions of page format in points
var $fw;
var $fh;             //dimensions of page format in user unit
var $wPt;
var $hPt;           //current dimensions of page in points
var $w;
var $h;               //current dimensions of page in user unit
var $lMargin;            //left margin
var $tMargin;            //top margin
var $rMargin;            //right margin
var $bMargin;            //page break margin
var $cMarginL;            //cell margin Left
var $cMarginR;            //cell margin Right
var $cMarginT;            //cell margin Left
var $cMarginB;            //cell margin Right
var $DeflMargin;            //Default left margin
var $DefrMargin;            //Default right margin
var $x;
var $y;               //current position in user unit for cell positioning
var $lasth;              //height of last cell printed
var $LineWidth;          //line width in user unit
var $CoreFonts;          //array of standard font names
var $fonts;              //array of used fonts
var $FontFiles;          //array of font files
var $images;             //array of used images
var $PageLinks;          //array of links in pages
var $links;              //array of internal links
var $FontFamily;         //current font family
var $FontStyle;          //current font style
var $CurrentFont;        //current font info
var $FontSizePt;         //current font size in points
var $FontSize;           //current font size in user unit
var $DrawColor;          //commands for drawing color
var $FillColor;          //commands for filling color
var $TextColor;          //commands for text color
var $ColorFlag;          //indicates whether fill and text colors are different
var $autoPageBreak;      //automatic page breaking
var $PageBreakTrigger;   //threshold used to trigger page breaks
var $InFooter;           //flag set when processing footer
var $InHTMLFooter;

var $processingFooter;   //flag set when processing footer - added for columns
var $processingHeader;   //flag set when processing header - added for columns
var $ZoomMode;           //zoom display mode
var $LayoutMode;         //layout display mode
var $title;              //title
var $subject;            //subject
var $author;             //author
var $keywords;           //keywords
var $creator;            //creator

var $aliasNbPg;       //alias for total number of pages
var $aliasNbPgGp;       //alias for total number of pages in page group
var $aliasNbPgHex;
var $aliasNbPgGpHex;

var $ispre;

var $outerblocktags;
var $innerblocktags;


// **********************************
// **********************************
// **********************************
// **********************************
// **********************************
// **********************************
// **********************************
// **********************************
// **********************************

function mPDF($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=0,$mgr=0,$mgt=56,$mgb=20,$mgh=0,$mgf=0, $orientation='P') {

		// mPDF 5.3.89 5.3.99
/*-- BACKGROUNDS --*/
		if (!class_exists('grad', false)) { include(_MPDF_PATH.'classes/grad.php'); }
		if (empty($this->grad)) { $this->grad = new grad($this); }
/*-- END BACKGROUNDS --*/
/*-- FORMS --*/
		if (!class_exists('form', false)) { include(_MPDF_PATH.'classes/form.php'); }
		if (empty($this->form)) { $this->form = new form($this); }
/*-- END FORMS --*/

	$this->time0 = microtime(true);
	//Some checks
	$this->_dochecks();

	// Set up Aliases for backwards compatability
	$this->UnvalidatedText =& $this->watermarkText;
	$this->TopicIsUnvalidated =& $this->showWatermarkText;
	$this->AliasNbPg =& $this->aliasNbPg;
	$this->AliasNbPgGp =& $this->aliasNbPgGp;
	$this->BiDirectional =& $this->biDirectional;
	$this->Anchor2Bookmark =& $this->anchor2Bookmark;
	$this->KeepColumns =& $this->keepColumns;
	$this->useOddEven =& $this->mirrorMargins;
	$this->useSubstitutionsMB =& $this->useSubstitutions;

	$this->visibility='visible';	// mPDF 5.3.41

	//Initialization of properties
	$this->spotColors=array();
	$this->spotColorIDs = array();
	$this->tableBackgrounds = array();

	$this->kt_y00 = '';
	$this->kt_p00 = '';
	$this->iterationCounter = false;
	$this->BMPonly = array();
	$this->page=0;
	$this->n=2;
	$this->buffer='';
	$this->objectbuffer = array();
	$this->pages=array();
	$this->OrientationChanges=array();
	$this->state=0;
	$this->fonts=array();
	$this->FontFiles=array();
	$this->images=array();
	$this->links=array();
	$this->InFooter=false;
	$this->processingFooter=false;
	$this->processingHeader=false;
	$this->lasth=0;
	$this->FontFamily='';
	$this->FontStyle='';
	$this->FontSizePt=9;
	$this->U=false;
	// Small Caps
	$this->upperCase = array();
	$this->S = false;
	$this->smCapsScale = 1;
	$this->smCapsStretch = 100;

	$this->defTextColor = $this->TextColor = $this->SetTColor($this->ConvertColor(0),true);
	$this->defDrawColor = $this->DrawColor = $this->SetDColor($this->ConvertColor(0),true);
	$this->defFillColor = $this->FillColor = $this->SetFColor($this->ConvertColor(255),true);

	//SVG color names array
	//http://www.w3schools.com/css/css_colornames.asp
	$this->SVGcolors = array('antiquewhite'=>'#FAEBD7','aqua'=>'#00FFFF','aquamarine'=>'#7FFFD4','beige'=>'#F5F5DC','black'=>'#000000',
'blue'=>'#0000FF','brown'=>'#A52A2A','cadetblue'=>'#5F9EA0','chocolate'=>'#D2691E','cornflowerblue'=>'#6495ED','crimson'=>'#DC143C',
'darkblue'=>'#00008B','darkgoldenrod'=>'#B8860B','darkgreen'=>'#006400','darkmagenta'=>'#8B008B','darkorange'=>'#FF8C00',
'darkred'=>'#8B0000','darkseagreen'=>'#8FBC8F','darkslategray'=>'#2F4F4F','darkviolet'=>'#9400D3','deepskyblue'=>'#00BFFF',
'dodgerblue'=>'#1E90FF','firebrick'=>'#B22222','forestgreen'=>'#228B22','fuchsia'=>'#FF00FF','gainsboro'=>'#DCDCDC','gold'=>'#FFD700',
'gray'=>'#808080','green'=>'#008000','greenyellow'=>'#ADFF2F','hotpink'=>'#FF69B4','indigo'=>'#4B0082','khaki'=>'#F0E68C',
'lavenderblush'=>'#FFF0F5','lemonchiffon'=>'#FFFACD','lightcoral'=>'#F08080','lightgoldenrodyellow'=>'#FAFAD2','lightgreen'=>'#90EE90',
'lightsalmon'=>'#FFA07A','lightskyblue'=>'#87CEFA','lightslategray'=>'#778899','lightyellow'=>'#FFFFE0','lime'=>'#00FF00','limegreen'=>'#32CD32',
'magenta'=>'#FF00FF','maroon'=>'#800000','mediumaquamarine'=>'#66CDAA','mediumorchid'=>'#BA55D3','mediumseagreen'=>'#3CB371',
'mediumspringgreen'=>'#00FA9A','mediumvioletred'=>'#C71585','midnightblue'=>'#191970','mintcream'=>'#F5FFFA','moccasin'=>'#FFE4B5','navy'=>'#000080',
'olive'=>'#808000','orange'=>'#FFA500','orchid'=>'#DA70D6','palegreen'=>'#98FB98',
'palevioletred'=>'#D87093','peachpuff'=>'#FFDAB9','pink'=>'#FFC0CB','powderblue'=>'#B0E0E6','purple'=>'#800080',
'red'=>'#FF0000','royalblue'=>'#4169E1','salmon'=>'#FA8072','seagreen'=>'#2E8B57','sienna'=>'#A0522D','silver'=>'#C0C0C0','skyblue'=>'#87CEEB',
'slategray'=>'#708090','springgreen'=>'#00FF7F','steelblue'=>'#4682B4','tan'=>'#D2B48C','teal'=>'#008080','thistle'=>'#D8BFD8','turquoise'=>'#40E0D0',
'violetred'=>'#D02090','white'=>'#FFFFFF','yellow'=>'#FFFF00', 
'aliceblue'=>'#f0f8ff', 'azure'=>'#f0ffff', 'bisque'=>'#ffe4c4', 'blanchedalmond'=>'#ffebcd', 'blueviolet'=>'#8a2be2', 'burlywood'=>'#deb887', 
'chartreuse'=>'#7fff00', 'coral'=>'#ff7f50', 'cornsilk'=>'#fff8dc', 'cyan'=>'#00ffff', 'darkcyan'=>'#008b8b', 'darkgray'=>'#a9a9a9', 
'darkgrey'=>'#a9a9a9', 'darkkhaki'=>'#bdb76b', 'darkolivegreen'=>'#556b2f', 'darkorchid'=>'#9932cc', 'darksalmon'=>'#e9967a', 
'darkslateblue'=>'#483d8b', 'darkslategrey'=>'#2f4f4f', 'darkturquoise'=>'#00ced1', 'deeppink'=>'#ff1493', 'dimgray'=>'#696969', 
'dimgrey'=>'#696969', 'floralwhite'=>'#fffaf0', 'ghostwhite'=>'#f8f8ff', 'goldenrod'=>'#daa520', 'grey'=>'#808080', 'honeydew'=>'#f0fff0', 
'indianred'=>'#cd5c5c', 'ivory'=>'#fffff0', 'lavender'=>'#e6e6fa', 'lawngreen'=>'#7cfc00', 'lightblue'=>'#add8e6', 'lightcyan'=>'#e0ffff', 
'lightgray'=>'#d3d3d3', 'lightgrey'=>'#d3d3d3', 'lightpink'=>'#ffb6c1', 'lightseagreen'=>'#20b2aa', 'lightslategrey'=>'#778899', 
'lightsteelblue'=>'#b0c4de', 'linen'=>'#faf0e6', 'mediumblue'=>'#0000cd', 'mediumpurple'=>'#9370db', 'mediumslateblue'=>'#7b68ee', 
'mediumturquoise'=>'#48d1cc', 'mistyrose'=>'#ffe4e1', 'navajowhite'=>'#ffdead', 'oldlace'=>'#fdf5e6', 'olivedrab'=>'#6b8e23', 'orangered'=>'#ff4500', 
'palegoldenrod'=>'#eee8aa', 'paleturquoise'=>'#afeeee', 'papayawhip'=>'#ffefd5', 'peru'=>'#cd853f', 'plum'=>'#dda0dd', 'rosybrown'=>'#bc8f8f', 
'saddlebrown'=>'#8b4513', 'sandybrown'=>'#f4a460', 'seashell'=>'#fff5ee', 'slateblue'=>'#6a5acd', 'slategrey'=>'#708090', 'snow'=>'#fffafa', 
'tomato'=>'#ff6347', 'violet'=>'#ee82ee', 'wheat'=>'#f5deb3', 'whitesmoke'=>'#f5f5f5', 'yellowgreen'=>'#9acd32');

	$this->ColorFlag=false;
	$this->extgstates = array();

	$this->mb_enc='windows-1252';
	$this->directionality='ltr';
	$this->defaultAlign = 'L';
	$this->defaultTableAlign = 'L';

	$this->fixedPosBlockSave = array();
	$this->extraFontSubsets = 0;

	$this->SHYpatterns = array();
	$this->loadedSHYdictionary = false;
	$this->SHYdictionary = array();
	$this->SHYdictionaryWords = array();
	$this->blockContext = 1;
	$this->floatDivs = array();
	$this->DisplayPreferences=''; 

	$this->tablecascadeCSS = array();
	$this->listcascadeCSS = array();

	$this->patterns = array();		// Tiling patterns used for backgrounds
	$this->pageBackgrounds = array();
	$this->writingHTMLheader = false;	// internal flag - used both for writing HTMLHeaders/Footers and FixedPos block
	$this->writingHTMLfooter = false;	// internal flag - used both for writing HTMLHeaders/Footers and FixedPos block
	$this->gradients = array();

	$this->kwt_Reference = array();
	$this->kwt_BMoutlines = array();
	$this->kwt_toc = array();

	$this->tbrot_Reference = array();
	$this->tbrot_BMoutlines = array();
	$this->tbrot_toc = array();

	$this->col_Reference = array();
	$this->col_BMoutlines = array();
	$this->col_toc = array();
	$this->graphs = array();

	$this->pgsIns = array();
	$this->PDFAXwarnings = array();
	$this->inlineDisplayOff = false;
	$this->kerning = false;
	$this->lSpacingCSS = '';
	$this->wSpacingCSS = '';
	$this->fixedlSpacing = false;
	$this->minwSpacing = 0;


	$this->baselineC = 0.35;	// Baseline for text
	$this->noImageFile = str_replace("\\","/",dirname(__FILE__)) . '/includes/no_image.jpg';
	$this->subPos = 0;
	$this->forceExactLineheight = false;
	$this->listOcc = 0;
	$this->normalLineheight = 1.3;
	// These are intended as configuration variables, and should be set in config.php - which will override these values; 
	// set here as failsafe as will cause an error if not defined
	$this->incrementFPR1 = 10;
	$this->incrementFPR2 = 10;
	$this->incrementFPR3 = 10;
	$this->incrementFPR4 = 10;

	$this->fullImageHeight = false;
	$this->floatbuffer = array();
	$this->floatmargins = array();
	$this->autoFontGroups = 0;
	$this->formobjects=array(); // array of Form Objects for WMF
	$this->InlineProperties=array();
	$this->InlineAnnots=array();
	$this->ktAnnots=array();
	$this->tbrot_Annots=array();
	$this->kwt_Annots=array();
	$this->columnAnnots=array();
	$this->pageDim=array();
	$this->breakpoints = array();	// used in columnbuffer
	$this->tableLevel=0;
	$this->tbctr=array();	// counter for nested tables at each level
	$this->page_box = array();
	$this->show_marks = '';	// crop or cross marks
	$this->kwt = false;
	$this->kwt_height = 0;
	$this->kwt_y0 = 0;
	$this->kwt_x0 = 0;
	$this->kwt_buffer = array();
	$this->kwt_Links = array();
	$this->kwt_moved = false;
	$this->kwt_saved = false;
	$this->PageNumSubstitutions = array();
	$this->base_table_properties=array();
	$this->borderstyles = array('inset','groove','outset','ridge','dotted','dashed','solid','double');
	$this->tbrot_align = 'C';
	$this->pageheaders=array();
	$this->pagefooters=array();

	$this->pageHTMLheaders=array();
	$this->pageHTMLfooters=array();
	$this->HTMLheaderPageLinks = array();
	$this->HTMLheaderPageAnnots = array();

	$this->ktForms = array();
	$this->HTMLheaderPageForms = array();
	$this->columnForms = array();
	$this->tbrotForms = array();
	$this->useRC128encryption = false;
	$this->uniqid = '';

	$this->cascadeCSS = array();
	$this->bufferoutput = false; 
	$this->encrypted=false;    		//whether document is protected
	$this->BMoutlines=array();
	$this->ColActive=0;        		//Flag indicating that columns are on (the index is being processed)
	$this->Reference=array();  		//Array containing the references
	$this->CurrCol=0;              	//Current column number
	$this->ColL = array(0);			// Array of Left pos of columns - absolute - needs Margin correction for Odd-Even
	$this->ColR = array(0);			// Array of Right pos of columns - absolute pos - needs Margin correction for Odd-Even
	$this->ChangeColumn = 0;
	$this->columnbuffer = array();
	$this->ColDetails = array();		// Keeps track of some column details
	$this->columnLinks = array();		// Cross references PageLinks
	$this->substitute = array();		// Array of substitution strings e.g. <ttz>112</ttz>
	$this->entsearch = array();		// Array of HTML entities (>ASCII 127) to substitute
	$this->entsubstitute = array();	// Array of substitution decimal unicode for the Hi entities
	$this->lastoptionaltag = '';
	$this->charset_in = '';
	$this->blk = array();
	$this->blklvl = 0;
	$this->tts = false;
	$this->ttz = false;
	$this->tta = false;
	$this->ispre=false;

	$this->checkSIP = false;
	$this->checkSMP = false;
	$this->checkCJK = false;
	$this->tableCJK = false;

	//$this->headerDetails=array();
	//$this->footerDetails=array();
	$this->page_break_after_avoid = false;
	$this->margin_bottom_collapse = false;
	$this->tablethead = 0;
	$this->tabletfoot = 0;
	$this->table_border_attr_set = 0;
	$this->table_border_css_set = 0;
	$this->shrin_k = 1.0;
	$this->shrink_this_table_to_fit = 0;
	$this->MarginCorrection = 0;

	$this->tabletheadjustfinished = false;
	$this->usingCoreFont = false;
	$this->charspacing=0;

	$this->outlines=array();
	$this->autoPageBreak = true;

	require(_MPDF_PATH.'config.php');	// config data

	$this->_setPageSize($format, $orientation);
	$this->DefOrientation=$orientation;

	$this->margin_header=$mgh;
	$this->margin_footer=$mgf;

	$bmargin=$mgb;

	$this->DeflMargin = $mgl;
	$this->DefrMargin = $mgr;

	$this->orig_tMargin = $mgt;
	$this->orig_bMargin = $bmargin;
	$this->orig_lMargin = $this->DeflMargin;
	$this->orig_rMargin = $this->DefrMargin;
	$this->orig_hMargin = $this->margin_header;
	$this->orig_fMargin = $this->margin_footer;

	if ($this->setAutoTopMargin=='pad') { $mgt += $this->margin_header; }
	if ($this->setAutoBottomMargin=='pad') { $mgb += $this->margin_footer; }
	$this->SetMargins($this->DeflMargin,$this->DefrMargin,$mgt);	// sets l r t margin
	//Automatic page break
	$this->SetAutoPageBreak($this->autoPageBreak,$bmargin);	// sets $this->bMargin & PageBreakTrigger

	$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;

	//Interior cell margin (1 mm) ? not used
	$this->cMarginL = 1;
	$this->cMarginR = 1;
	//Line width (0.2 mm)
	$this->LineWidth=.567/_MPDFK;

	//To make the function Footer() work - replaces {nb} with page number
	$this->AliasNbPages();
	$this->AliasNbPageGroups();

	$this->aliasNbPgHex = '{nbHEXmarker}';
	$this->aliasNbPgGpHex = '{nbpgHEXmarker}';

	//Enable all tags as default
	$this->DisableTags();
	//Full width display mode
	$this->SetDisplayMode(100);	// fullwidth?		'fullpage'
	//Compression
	$this->SetCompression(true);
	//Set default display preferences
	$this->SetDisplayPreferences(''); 

	// Font data
	require(_MPDF_PATH.'config_fonts.php');
	// Available fonts
	$this->available_unifonts = array();
	foreach ($this->fontdata AS $f => $fs) {
		if (isset($fs['R']) && $fs['R']) { $this->available_unifonts[] = $f; }
		if (isset($fs['B']) && $fs['B']) { $this->available_unifonts[] = $f.'B'; }
		if (isset($fs['I']) && $fs['I']) { $this->available_unifonts[] = $f.'I'; }
		if (isset($fs['BI']) && $fs['BI']) { $this->available_unifonts[] = $f.'BI'; }
	}

	$this->default_available_fonts = $this->available_unifonts;

	$optcore = false;
	$onlyCoreFonts = false;
	if (preg_match('/([\-+])aCJK/i',$mode, $m)) {
		preg_replace('/([\-+])aCJK/i','',$mode);
		if ($m[1]=='+') { $this->useAdobeCJK = true; }
		else { $this->useAdobeCJK = false; }
	}

	if (strlen($mode)==1) {
		if ($mode=='s') { $this->percentSubset = 100; $mode = ''; }
		else if ($mode=='c') { $onlyCoreFonts = true; $mode = ''; }
	}
	else if (substr($mode,-2)=='-s') {
		$this->percentSubset = 100; 
		$mode = substr($mode,0,strlen($mode)-2);
	}
	else if (substr($mode,-2)=='-c') {
		$onlyCoreFonts = true;
		$mode = substr($mode,0,strlen($mode)-2);
	}
	else if (substr($mode,-2)=='-x') {
		$optcore = true;
		$mode = substr($mode,0,strlen($mode)-2);
	}

	// Autodetect if mode is a language_country string (en-GB or en_GB or en)
	if ((strlen($mode) == 5 && $mode != 'UTF-8') || strlen($mode) == 2) {
		list ($coreSuitable,$mpdf_pdf_unifonts) = GetLangOpts($mode, $this->useAdobeCJK);
		if ($coreSuitable && $optcore) { $onlyCoreFonts = true; }
		if ($mpdf_pdf_unifonts) { 
			$this->RestrictUnicodeFonts($mpdf_pdf_unifonts); 
			$this->default_available_fonts = $mpdf_pdf_unifonts;
		}
		$this->currentLang = $mode;
		$this->default_lang = $mode;
	}

	$this->onlyCoreFonts =  $onlyCoreFonts;

	if ($this->onlyCoreFonts) {
		$this->setMBencoding('windows-1252');	// sets $this->mb_enc
	}
	else {
		$this->setMBencoding('UTF-8');	// sets $this->mb_enc
	}
	@mb_regex_encoding('UTF-8'); 	// mPDF 5.3.07	// required only for mb_ereg... and mb_split functions


	// Adobe CJK fonts
	$this->available_CJK_fonts = array('gb','big5','sjis','uhc','gbB','big5B','sjisB','uhcB','gbI','big5I','sjisI','uhcI',
		'gbBI','big5BI','sjisBI','uhcBI');


	//Standard fonts
	$this->CoreFonts=array('ccourier'=>'Courier','ccourierB'=>'Courier-Bold','ccourierI'=>'Courier-Oblique','ccourierBI'=>'Courier-BoldOblique',
		'chelvetica'=>'Helvetica','chelveticaB'=>'Helvetica-Bold','chelveticaI'=>'Helvetica-Oblique','chelveticaBI'=>'Helvetica-BoldOblique',
		'ctimes'=>'Times-Roman','ctimesB'=>'Times-Bold','ctimesI'=>'Times-Italic','ctimesBI'=>'Times-BoldItalic',
		'csymbol'=>'Symbol','czapfdingbats'=>'ZapfDingbats');
	$this->fontlist=array("ctimes","ccourier","chelvetica","csymbol","czapfdingbats");

	// Substitutions
	$this->setHiEntitySubstitutions();

	if ($this->onlyCoreFonts) {
		$this->useSubstitutions = true;
		$this->SetSubstitutions();
	}
	else { $this->useSubstitutions = false; }

/*-- HTML-CSS --*/
	if (file_exists(_MPDF_PATH.'mpdf.css')) {
		$css = file_get_contents(_MPDF_PATH.'mpdf.css');
		$css2 = $this->ReadDefaultCSS($css);
		$this->defaultCSS = $this->array_merge_recursive_unique($this->defaultCSS,$css2); 
	}
/*-- END HTML-CSS --*/

	if ($default_font=='') { 
	  if ($this->onlyCoreFonts) { 
		if (in_array(strtolower($this->defaultCSS['BODY']['FONT-FAMILY']),$this->mono_fonts)) { $default_font = 'ccourier'; }
		else if (in_array(strtolower($this->defaultCSS['BODY']['FONT-FAMILY']),$this->sans_fonts)) { $default_font = 'chelvetica'; }
		else { $default_font = 'ctimes'; }
	  }
	  else { $default_font = $this->defaultCSS['BODY']['FONT-FAMILY']; }
	}
	if (!$default_font_size) { 
		$mmsize = $this->ConvertSize($this->defaultCSS['BODY']['FONT-SIZE']);
		$default_font_size = $mmsize*(_MPDFK);
	}

	if ($default_font) { $this->SetDefaultFont($default_font); }
	if ($default_font_size) { $this->SetDefaultFontSize($default_font_size); }

	$this->SetLineHeight();	// lineheight is in mm

	$this->SetFColor($this->ConvertColor(255));
	$this->HREF='';
	$this->oldy=-1;
	$this->B=0;
	$this->U=false;
	$this->S=false;
	$this->I=0;

	$this->listlvl=0;
	$this->listnum=0; 
	$this->listtype='';
	$this->listoccur=array();
	$this->listlist=array();
	$this->listitem=array();

	$this->tdbegin=false; 
	$this->table=array(); 
	$this->cell=array();  
	$this->col=-1; 
	$this->row=-1; 
	$this->cellBorderBuffer = array();

	$this->divbegin=false;
	$this->divalign='';
	$this->divwidth=0; 
	$this->divheight=0; 
	$this->spanbgcolor=false;
	$this->divrevert=false;
	// mPDF 5.3.61
	$this->spanborder=false;
	$this->spanborddet=array();

	$this->blockjustfinished=false;
	$this->listjustfinished=false;
	$this->ignorefollowingspaces = true; //in order to eliminate exceeding left-side spaces
	$this->toupper=false;
	$this->tolower=false;
	$this->capitalize=false;
	$this->dash_on=false;
	$this->dotted_on=false;
	$this->SUP=false;
	$this->SUB=false;
	$this->strike=false;
	$this->textshadow='';	// mPDF 5.3.A2

	$this->currentfontfamily='';
	$this->currentfontsize='';
	$this->currentfontstyle='';
	$this->colorarray=array();
	$this->spanbgcolorarray=array();
	$this->textbuffer=array();
	$this->CSS=array();
	$this->internallink=array();
	$this->basepath = "";

	$this->SetBasePath('');

	$this->outlineparam = array();
	$this->outline_on = false;

	$this->specialcontent = '';
	$this->selectoption = array();

/*-- IMPORTS --*/

	$this->tpls = array();
	$this->tpl = 0;
	$this->tplprefix = "/TPL";
	$this->res = array();
	if ($this->enableImports) {
		$this->SetImportUse();
	}
/*-- END IMPORTS --*/

	if ($this->progressBar) { $this->StartProgressBarOutput($this->progressBar) ;	}	// *PROGRESS-BAR*
}


function _setPageSize($format, &$orientation) {
	//Page format
	if(is_string($format))
	{
		if ($format=='') { $format = 'A4'; }
		$pfo = 'P';
		if(preg_match('/([0-9a-zA-Z]*)-L/i',$format,$m)) {	// e.g. A4-L = A4 landscape
			$format=$m[1]; 
			$pfo='L'; 
		}
		$format = $this->_getPageFormat($format);
		if (!$format) { $this->Error('Unknown page format: '.$format); }
		else { $orientation = $pfo; }

		$this->fwPt=$format[0];
		$this->fhPt=$format[1];
	}
	else
	{
		if (!$format[0] || !$format[1]) { $this->Error('Invalid page format: '.$format[0].' '.$format[1]); }
		$this->fwPt=$format[0]*_MPDFK;
		$this->fhPt=$format[1]*_MPDFK;
	}
	$this->fw=$this->fwPt/_MPDFK;
	$this->fh=$this->fhPt/_MPDFK;
	//Page orientation
	$orientation=strtolower($orientation);
	if($orientation=='p' or $orientation=='portrait')
	{
		$orientation='P';
		$this->wPt=$this->fwPt;
		$this->hPt=$this->fhPt;
	}
	elseif($orientation=='l' or $orientation=='landscape')
	{
		$orientation='L';
		$this->wPt=$this->fhPt;
		$this->hPt=$this->fwPt;
	}
	else $this->Error('Incorrect orientation: '.$orientation);
	$this->CurOrientation=$orientation;

	$this->w=$this->wPt/_MPDFK;
	$this->h=$this->hPt/_MPDFK;
}

function _getPageFormat($format) {
		switch (strtoupper($format)) {
			case '4A0': {$format = array(4767.87,6740.79); break;}
			case '2A0': {$format = array(3370.39,4767.87); break;}
			case 'A0': {$format = array(2383.94,3370.39); break;}
			case 'A1': {$format = array(1683.78,2383.94); break;}
			case 'A2': {$format = array(1190.55,1683.78); break;}
			case 'A3': {$format = array(841.89,1190.55); break;}
			case 'A4': default: {$format = array(595.28,841.89); break;}
			case 'A5': {$format = array(419.53,595.28); break;}
			case 'A6': {$format = array(297.64,419.53); break;}
			case 'A7': {$format = array(209.76,297.64); break;}
			case 'A8': {$format = array(147.40,209.76); break;}
			case 'A9': {$format = array(104.88,147.40); break;}
			case 'A10': {$format = array(73.70,104.88); break;}
			case 'B0': {$format = array(2834.65,4008.19); break;}
			case 'B1': {$format = array(2004.09,2834.65); break;}
			case 'B2': {$format = array(1417.32,2004.09); break;}
			case 'B3': {$format = array(1000.63,1417.32); break;}
			case 'B4': {$format = array(708.66,1000.63); break;}
			case 'B5': {$format = array(498.90,708.66); break;}
			case 'B6': {$format = array(354.33,498.90); break;}
			case 'B7': {$format = array(249.45,354.33); break;}
			case 'B8': {$format = array(175.75,249.45); break;}
			case 'B9': {$format = array(124.72,175.75); break;}
			case 'B10': {$format = array(87.87,124.72); break;}
			case 'C0': {$format = array(2599.37,3676.54); break;}
			case 'C1': {$format = array(1836.85,2599.37); break;}
			case 'C2': {$format = array(1298.27,1836.85); break;}
			case 'C3': {$format = array(918.43,1298.27); break;}
			case 'C4': {$format = array(649.13,918.43); break;}
			case 'C5': {$format = array(459.21,649.13); break;}
			case 'C6': {$format = array(323.15,459.21); break;}
			case 'C7': {$format = array(229.61,323.15); break;}
			case 'C8': {$format = array(161.57,229.61); break;}
			case 'C9': {$format = array(113.39,161.57); break;}
			case 'C10': {$format = array(79.37,113.39); break;}
			case 'RA0': {$format = array(2437.80,3458.27); break;}
			case 'RA1': {$format = array(1729.13,2437.80); break;}
			case 'RA2': {$format = array(1218.90,1729.13); break;}
			case 'RA3': {$format = array(864.57,1218.90); break;}
			case 'RA4': {$format = array(609.45,864.57); break;}
			case 'SRA0': {$format = array(2551.18,3628.35); break;}
			case 'SRA1': {$format = array(1814.17,2551.18); break;}
			case 'SRA2': {$format = array(1275.59,1814.17); break;}
			case 'SRA3': {$format = array(907.09,1275.59); break;}
			case 'SRA4': {$format = array(637.80,907.09); break;}
			case 'LETTER': {$format = array(612.00,792.00); break;}
			case 'LEGAL': {$format = array(612.00,1008.00); break;}
			case 'LEDGER': {$format = array(279.00,432.00); break;}	// mPDF 5.3.38
			case 'TABLOID': {$format = array(279.00,432.00); break;}	// mPDF 5.3.38
			case 'EXECUTIVE': {$format = array(521.86,756.00); break;}
			case 'FOLIO': {$format = array(612.00,936.00); break;}
			case 'B': {$format=array(362.83,561.26 );	 break;}		//	'B' format paperback size 128x198mm
			case 'A': {$format=array(314.65,504.57 );	 break;}		//	'A' format paperback size 111x178mm
			case 'DEMY': {$format=array(382.68,612.28 );  break;}		//	'Demy' format paperback size 135x216mm
			case 'ROYAL': {$format=array(433.70,663.30 );  break;}	//	'Royal' format paperback size 153x234mm
			default: $format = false;
		}
	return $format;
}


/*-- PROGRESS-BAR --*/
function StartProgressBarOutput($mode=1) {
	// must be relative path, or URI (not a file system path)
	if (!defined('_MPDF_URI')) { 
		$this->progressBar = false;
		if ($this->debug) { $this->Error("You need to define _MPDF_URI to use the progress bar!"); }
		else return false; 
	}
	$this->progressBar = $mode;
	if ($this->progbar_altHTML) {
		echo $this->progbar_altHTML;
	}
	else {
	   echo '<html>
	<head>
	<title>mPDF File Progress</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="'._MPDF_URI.'progbar.css" />
		</head>
	<body>
	<div class="main">
		<div class="heading">'.$this->progbar_heading.'</div>
		<div class="demo">
	   ';
	   if ($this->progressBar==2) { echo '		<table width="100%"><tr><td style="width: 50%;"> 
			<span class="barheading">Writing HTML code</span> <br/>

			<div class="progressBar">
			<div id="element1"  class="innerBar">&nbsp;</div>
			</div>
			<span class="code" id="box1"></span>
			</td><td style="width: 50%;">
			<span class="barheading">Autosizing elements</span> <br/>
			<div class="progressBar">
			<div id="element4"  class="innerBar">&nbsp;</div>
			</div>
			<span class="code" id="box4"></span>
			<br/><br/>
			<span class="barheading">Writing Tables</span> <br/>
			<div class="progressBar">
			<div id="element7"  class="innerBar">&nbsp;</div>
			</div>
			<span class="code" id="box7"></span>
			</td></tr>
			<tr><td><br /><br /></td><td></td></tr>
			<tr><td style="width: 50%;"> 
	'; }
	echo '			<span class="barheading">Writing PDF file</span> <br/>
			<div class="progressBar">
			<div id="element2"  class="innerBar">&nbsp;</div>
			</div>
			<span class="code" id="box2"></span>
	   ';
	   if ($this->progressBar==2) { echo '
			</td><td style="width: 50%;">
			<span class="barheading">Memory usage</span> <br/>
			<div class="progressBar">
			<div id="element5"  class="innerBar">&nbsp;</div>
			</div>
			<span id="box5">0</span> '.ini_get("memory_limit").'<br />
			<br/><br/>
			<span class="barheading">Memory usage (peak)</span> <br/>
			<div class="progressBar">
			<div id="element6"  class="innerBar">&nbsp;</div>
			</div>
			<span id="box6">0</span> '.ini_get("memory_limit").'<br />
			</td></tr>
			</table>
	   '; }
	   echo '			<br/><br/>
		<span id="box3"></span>

		</div>
	   ';
	}
	ob_flush();
      flush();
}

function UpdateProgressBar($el,$val,$txt='') {
	// $val should be a string - 5 = actual value, +15 = increment

	if ($this->progressBar<2) {
		if ($el>3) { return; }
		else if ($el ==1) { $el = 2; }
	}
	echo '<script type="text/javascript">';
	if ($val) { echo ' document.getElementById(\'element'.$el.'\').style.width=\''.$val.'%\'; '; }
	if ($txt) { echo ' document.getElementById(\'box'.$el.'\').innerHTML=\''.$txt.'\'; '; }
	if ($this->progressBar==2) { 
		$m = round(memory_get_usage(true)/1048576);	// mPDF 5.3.72
		$m2 = round(memory_get_peak_usage(true)/1048576);	// mPDF 5.3.72
		$mem = $m * 100 / (ini_get("memory_limit")+0);
		$mem2 = $m2 * 100 / (ini_get("memory_limit")+0);
		echo ' document.getElementById(\'element5\').style.width=\''.$mem.'%\'; '; 
		echo ' document.getElementById(\'element6\').style.width=\''.$mem2.'%\'; '; 
		echo ' document.getElementById(\'box5\').innerHTML=\''.$m.'MB / \'; '; 
		echo ' document.getElementById(\'box6\').innerHTML=\''.$m2.'MB / \'; '; 
	}
	echo '</script>'."\n";
	ob_flush();
	flush();
}
/*-- END PROGRESS-BAR --*/



function RestrictUnicodeFonts($res) {
	// $res = array of (Unicode) fonts to restrict to: e.g. norasi|norasiB - language specific
	if (count($res)) {	// Leave full list of available fonts if passed blank array
		$this->available_unifonts = $res;
	}
	else { $this->available_unifonts = $this->default_available_fonts; }
	if (count($this->available_unifonts) == 0) { $this->available_unifonts[] = $this->default_available_fonts[0]; }
	$this->available_unifonts = array_values($this->available_unifonts);
}


function setMBencoding($enc) {
	// mPDF 5.3.07
	if ($this->mb_enc != $enc) { 
		$this->mb_enc = $enc; 
		mb_internal_encoding($this->mb_enc); 
	}
}


function SetMargins($left,$right,$top) {
	//Set left, top and right margins
	$this->lMargin=$left;
	$this->rMargin=$right;
	$this->tMargin=$top;
}

function ResetMargins() {
	//ReSet left, top margins
	if (($this->forcePortraitHeaders || $this->forcePortraitMargins) && $this->DefOrientation=='P' && $this->CurOrientation=='L') {
	    if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
		$this->tMargin=$this->orig_rMargin;
		$this->bMargin=$this->orig_lMargin;
	    }
	    else {	// ODD	// OR NOT MIRRORING MARGINS/FOOTERS
		$this->tMargin=$this->orig_lMargin;
		$this->bMargin=$this->orig_rMargin;
	    }
	   $this->lMargin=$this->DeflMargin;
	   $this->rMargin=$this->DefrMargin;
	   $this->MarginCorrection = 0;
	   $this->PageBreakTrigger=$this->h-$this->bMargin;
	}
	else  if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
		$this->lMargin=$this->DefrMargin;
		$this->rMargin=$this->DeflMargin;
		$this->MarginCorrection = $this->DefrMargin-$this->DeflMargin;

	}
	else {	// ODD	// OR NOT MIRRORING MARGINS/FOOTERS
		$this->lMargin=$this->DeflMargin;
		$this->rMargin=$this->DefrMargin;
		if ($this->mirrorMargins) { $this->MarginCorrection = $this->DeflMargin-$this->DefrMargin; }
	}
	$this->x=$this->lMargin;

}

function SetLeftMargin($margin) {
	//Set left margin
	$this->lMargin=$margin;
	if($this->page>0 and $this->x<$margin) $this->x=$margin;
}

function SetTopMargin($margin) {
	//Set top margin
	$this->tMargin=$margin;
}

function SetRightMargin($margin) {
	//Set right margin
	$this->rMargin=$margin;
}

function SetAutoPageBreak($auto,$margin=0) {
	//Set auto page break mode and triggering margin
	$this->autoPageBreak=$auto;
	$this->bMargin=$margin;
	$this->PageBreakTrigger=$this->h-$margin;
}

function SetDisplayMode($zoom,$layout='continuous') {
	//Set display mode in viewer
	if($zoom=='fullpage' or $zoom=='fullwidth' or $zoom=='real' or $zoom=='default' or !is_string($zoom))
		$this->ZoomMode=$zoom;
	else
		$this->Error('Incorrect zoom display mode: '.$zoom);
	if($layout=='single' or $layout=='continuous' or $layout=='two' or $layout=='twoleft' or $layout=='tworight' or $layout=='default')
		$this->LayoutMode=$layout;
	else
		$this->Error('Incorrect layout display mode: '.$layout);
}

function SetCompression($compress) {
	//Set page compression
	if(function_exists('gzcompress'))	$this->compress=$compress;
	else $this->compress=false;
}

function SetTitle($title) {
	//Title of document // Arrives as UTF-8
	$this->title = $title;
}

function SetSubject($subject) {
	//Subject of document
	$this->subject= $subject;
}

function SetAuthor($author) {
	//Author of document
	$this->author= $author;
}

function SetKeywords($keywords) {
	//Keywords of document
	$this->keywords= $keywords;
}

function SetCreator($creator) {
	//Creator of document
	$this->creator= $creator;
}


function SetAnchor2Bookmark($x) {
	$this->anchor2Bookmark = $x;
}

function AliasNbPages($alias='{nb}') {
	//Define an alias for total number of pages
	$this->aliasNbPg=$alias;
}

function AliasNbPageGroups($alias='{nbpg}') {
	//Define an alias for total number of pages in a group
	$this->aliasNbPgGp=$alias;
}

function SetAlpha($alpha, $bm='Normal', $return=false, $mode='B') {
// alpha: real value from 0 (transparent) to 1 (opaque)
// bm:    blend mode, one of the following:
//          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
//          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
// set alpha for stroking (CA) and non-stroking (ca) operations
// mode determines F (fill) S (stroke) B (both)
	if (($this->PDFA || $this->PDFX) && $alpha!=1) { 
		if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) { $this->PDFAXwarnings[] = "Image opacity must be 100% (Opacity changed to 100%)"; }
		$alpha = 1; 
	}
	$a = array('BM'=>'/'.$bm);
	if ($mode=='F' || $mode='B') $a['ca'] = $alpha;
	if ($mode=='S' || $mode='B') $a['CA'] = $alpha;
	$gs = $this->AddExtGState($a);
	if ($return) { return sprintf('/GS%d gs', $gs); }
	else { $this->_out(sprintf('/GS%d gs', $gs)); }
}

function AddExtGState($parms) {
	$n = count($this->extgstates);
	// check if graphics state already exists
	for ($i=1; $i<=$n; $i++) {
	  if (count($this->extgstates[$i]['parms']) == count($parms)) {
	    $same = true;
	    foreach($this->extgstates[$i]['parms'] AS $k=>$v) {
		if (!isset($parms[$k]) || $parms[$k] != $v) { $same = false; break; }
	    }
	    if ($same) { return $i; }
	  }
	}
	$n++;
	$this->extgstates[$n]['parms'] = $parms;
	return $n;
}

// mPDF 5.3.41
function SetVisibility($v) {
	if (($this->PDFA || $this->PDFX) && $this->visibility!='visible') { $this->PDFAXwarnings[] = "Cannot set visibility to anything other than full when using PDFA or PDFX"; return ''; }
	else if (!$this->PDFA && !$this->PDFX)	// mPDF 5.3.45
		$this->pdf_version='1.5';
	if($this->visibility!='visible') {
		$this->_out('EMC');
		$this->hasOC = true;	// mPDF 5.3.45
	}
	if($v=='printonly') 
		$this->_out('/OC /OC1 BDC');
	elseif($v=='screenonly')
		$this->_out('/OC /OC2 BDC');
	elseif($v=='hidden')
		$this->_out('/OC /OC3 BDC');
	elseif($v!='visible')
		$this->Error('Incorrect visibility: '.$v);
	$this->visibility=$v;
}

function Error($msg) {
	//Fatal error
	header('Content-Type: text/html; charset=utf-8');
	die('<B>mPDF error: </B>'.$msg);
}

function Open() {
	//Begin document
	if($this->state==0)	$this->_begindoc();
}

function Close() {
	if ($this->progressBar) { $this->UpdateProgressBar(2,'2','Closing last page'); }	// *PROGRESS-BAR*
	//Terminate document
	if($this->state==3)	return;
	if($this->page==0) $this->AddPage($this->CurOrientation);
	if (count($this->cellBorderBuffer)) { $this->printcellbuffer(); }	// *TABLES*
	if ($this->tablebuffer) { $this->printtablebuffer(); }	// *TABLES*
/*-- COLUMNS --*/

	if ($this->ColActive) {
		$this->SetColumns(0);
		$this->ColActive = 0; 
		if (count($this->columnbuffer)) { $this->printcolumnbuffer(); }
	}
/*-- END COLUMNS --*/
	if (count($this->divbuffer)) { $this->printdivbuffer(); }

	// BODY Backgrounds
	$s = '';

	$s .= $this->PrintBodyBackgrounds();

	$s .= $this->PrintPageBackgrounds();
	$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', "\n".$s."\n".'\\1', $this->pages[$this->page]);
	$this->pageBackgrounds = array();

	// mPDF 5.3.41
	if($this->visibility!='visible')
		$this->SetVisibility('visible');

	if (!$this->tocontents || !$this->tocontents->TOCmark) { //Page footer
		$this->InFooter=true;
		$this->Footer();
		$this->InFooter=false;
	}
	if ($this->tocontents && ($this->tocontents->TOCmark || count($this->tocontents->m_TOC))) { $this->tocontents->insertTOC(); }	// *TOC*

	//Close page
	$this->_endpage();

	//Close document
	$this->_enddoc();
}

/*-- BACKGROUNDS --*/
function _resizeBackgroundImage($imw, $imh, $cw, $ch, $resize=0, $repx, $repy) {
	$cw = $cw*_MPDFK;
	$ch = $ch*_MPDFK;
	if (!$resize) { return array($imw, $imh, $repx, $repy); }
	if ($resize==1 && $imw > $cw) {
		$h = $imh * $cw/$imw;
		$repx = false;
		return array($cw, $h, $repx, $repy); 
	}
	else if ($resize==2 && $imh > $ch) {
		$w = $imw * $ch/$imh;
		$repy = false;
		return array($w, $ch, $repx, $repy); 
	}
	else if ($resize==3) {
		$w = $imw;
		$h = $imh;
		$saverepx = $repx;
		if ($w > $cw) {
			$h = $h * $cw/$w;
			$w = $cw;
			$repx = false;
		}
		if ($h > $ch) {
			$w = $w * $ch/$h;
			$h = $ch;
			$repy = false;
			$repx = $saverepx;
		}
		return array($w, $h, $repx, $repy); 
	}
	else if ($resize==4) {
		$h = $imh * $cw/$imw;
		$repx = false;
		return array($cw, $h, $repx, $repy); 
	}
	else if ($resize==5) {
		$w = $imw * $ch/$imh;
		$repy = false;
		return array($w, $ch, $repx, $repy); 
	}
	else if ($resize==6) {
		$repx = false;
		$repy = false;
		return array($cw, $ch, $repx, $repy); 
	}
	return array($imw, $imh, $repx, $repy);
}


function SetBackground(&$properties, &$maxwidth) {
	   if (preg_match('/(-moz-)*(repeating-)*(linear|radial)-gradient/',$properties['BACKGROUND-IMAGE'])) {
		return array('gradient'=>$properties['BACKGROUND-IMAGE']);
	   }
	   else {
		$file = $properties['BACKGROUND-IMAGE'];
		$sizesarray = $this->Image($file,0,0,0,0,'','',false, false, false, false, true);
		if (isset($sizesarray['IMAGE_ID'])) {
			$image_id = $sizesarray['IMAGE_ID'];
			$orig_w = $sizesarray['WIDTH']*_MPDFK;		// in user units i.e. mm
 			$orig_h = $sizesarray['HEIGHT']*_MPDFK;		// (using $this->img_dpi)
			if (isset($properties['BACKGROUND-IMAGE-RESOLUTION'])) { 
				if (preg_match('/from-image/i', $properties['BACKGROUND-IMAGE-RESOLUTION']) && isset($sizesarray['set-dpi']) && $sizesarray['set-dpi']>0) {
					$orig_w *= $this->img_dpi / $sizesarray['set-dpi'];
					$orig_h *= $this->img_dpi / $sizesarray['set-dpi'];
				}
				else if (preg_match('/(\d+)dpi/i', $properties['BACKGROUND-IMAGE-RESOLUTION'], $m)) {
					$dpi = $m[1]; 
					if ($dpi > 0) {
						$orig_w *= $this->img_dpi / $dpi;
						$orig_h *= $this->img_dpi / $dpi;
					}
				}
			}
			$x_repeat = true;
			$y_repeat = true;
			if (isset($properties['BACKGROUND-REPEAT'])) {
				if ($properties['BACKGROUND-REPEAT']=='no-repeat' || $properties['BACKGROUND-REPEAT']=='repeat-x') { $y_repeat = false; }
				if ($properties['BACKGROUND-REPEAT']=='no-repeat' || $properties['BACKGROUND-REPEAT']=='repeat-y') { $x_repeat = false; }
			}
			$x_pos = 0;
			$y_pos = 0;
			if (isset($properties['BACKGROUND-POSITION'])) { 
				$ppos = preg_split('/\s+/',$properties['BACKGROUND-POSITION']);
				$x_pos = $ppos[0];
				$y_pos = $ppos[1];
				if (!stristr($x_pos ,'%') ) { $x_pos = $this->ConvertSize($x_pos ,$maxwidth,$this->FontSize); }
				if (!stristr($y_pos ,'%') ) { $y_pos = $this->ConvertSize($y_pos ,$maxwidth,$this->FontSize); }
			}
			if (isset($properties['BACKGROUND-IMAGE-RESIZE'])) { $resize = $properties['BACKGROUND-IMAGE-RESIZE']; }
			else { $resize = 0; }
			if (isset($properties['BACKGROUND-IMAGE-OPACITY'])) { $opacity = $properties['BACKGROUND-IMAGE-OPACITY']; }
			else { $opacity = 1; }
			return array('image_id'=>$image_id, 'orig_w'=>$orig_w, 'orig_h'=>$orig_h, 'x_pos'=>$x_pos, 'y_pos'=>$y_pos, 'x_repeat'=>$x_repeat, 'y_repeat'=>$y_repeat, 'resize'=>$resize, 'opacity'=>$opacity, 'itype'=>$sizesarray['itype']);
		}
	   }
	   return false;
}
/*-- END BACKGROUNDS --*/

function PrintBodyBackgrounds() {
	$s = '';
	$clx = 0;
	$cly = 0;
	$clw = $this->w;
	$clh = $this->h;
	// If using bleed and trim margins in paged media
	if ($this->pageDim[$this->page]['outer_width_LR'] || $this->pageDim[$this->page]['outer_width_TB']) {
		$clx = $this->pageDim[$this->page]['outer_width_LR'] - $this->pageDim[$this->page]['bleedMargin'];
		$cly = $this->pageDim[$this->page]['outer_width_TB'] - $this->pageDim[$this->page]['bleedMargin'];
		$clw = $this->w - 2*$clx;
		$clh = $this->h - 2*$cly;
	}

	if ($this->bodyBackgroundColor) {
		$s .= 'q ' .$this->SetFColor($this->bodyBackgroundColor, true)."\n";
		// mPDF 5.3.74
		if ($this->bodyBackgroundColor{0}==5) {	// RGBa
			$s .= $this->SetAlpha(ord($this->bodyBackgroundColor{4})/100, 'Normal', true, 'F')."\n";
		}
		else if ($this->bodyBackgroundColor{0}==6) {	// CMYKa
			$s .= $this->SetAlpha(ord($this->bodyBackgroundColor{5})/100, 'Normal', true, 'F')."\n";
		}
		$s .= sprintf('%.3F %.3F %.3F %.3F re f Q', ($clx*_MPDFK), ($cly*_MPDFK),$clw*_MPDFK,$clh*_MPDFK)."\n";
	}

/*-- BACKGROUNDS --*/
	if ($this->bodyBackgroundGradient) { 
		$g = $this->grad->parseBackgroundGradient($this->bodyBackgroundGradient);
		if ($g) {
			$s .= $this->grad->Gradient($clx, $cly, $clw, $clh, (isset($g['gradtype']) ? $g['gradtype'] : null), $g['stops'], $g['colorspace'], $g['coords'], $g['extend'], true);
		}
	}
	if ($this->bodyBackgroundImage) {
	   if ( $this->bodyBackgroundImage['gradient'] && preg_match('/(-moz-)*(repeating-)*(linear|radial)-gradient/', $this->bodyBackgroundImage['gradient'])) {
		$g = $this->grad->parseMozGradient( $this->bodyBackgroundImage['gradient']);
		if ($g) {
			$s .= $this->grad->Gradient($clx, $cly, $clw, $clh, $g['type'], $g['stops'], $g['colorspace'], $g['coords'], $g['extend'], true);
		}
	   }
	   else if ($this->bodyBackgroundImage['image_id']) {	// Background pattern
			$n = count($this->patterns)+1;
			// If using resize, uses TrimBox (not including the bleed)
			list($orig_w, $orig_h, $x_repeat, $y_repeat) = $this->_resizeBackgroundImage($this->bodyBackgroundImage['orig_w'], $this->bodyBackgroundImage['orig_h'], $clw, $clh, $this->bodyBackgroundImage['resize'], $this->bodyBackgroundImage['x_repeat'], $this->bodyBackgroundImage['y_repeat']);

			$this->patterns[$n] = array('x'=>$clx, 'y'=>$cly, 'w'=>$clw, 'h'=>$clh, 'pgh'=>$this->h, 'image_id'=>$this->bodyBackgroundImage['image_id'], 'orig_w'=>$orig_w, 'orig_h'=>$orig_h, 'x_pos'=>$this->bodyBackgroundImage['x_pos'], 'y_pos'=>$this->bodyBackgroundImage['y_pos'], 'x_repeat'=>$x_repeat, 'y_repeat'=>$y_repeat, 'itype'=>$this->bodyBackgroundImage['itype']);
			// mPDF 5.3.12
			if (($this->bodyBackgroundImage['opacity']>0 || $this->bodyBackgroundImage['opacity']==='0') && $this->bodyBackgroundImage['opacity']<1) { $opac = $this->SetAlpha($this->bodyBackgroundImage['opacity'],'Normal',true); }
			else { $opac = ''; }
			$s .= sprintf('q /Pattern cs /P%d scn %s %.3F %.3F %.3F %.3F re f Q', $n, $opac, ($clx*_MPDFK), ($cly*_MPDFK),$clw*_MPDFK, $clh*_MPDFK) ."\n";
	   }
	}
/*-- END BACKGROUNDS --*/
	return $s;
}


function PrintPageBackgrounds($adjustmenty=0) {
	$s = '';

	ksort($this->pageBackgrounds);
	foreach($this->pageBackgrounds AS $bl=>$pbs) {
		foreach ($pbs AS $pb) {
		  // mPDF 5.3.A1
		  if ((!isset($pb['image_id']) && !isset($pb['gradient'])) || isset($pb['shadowonly'])) {	// Background colour or boxshadow
			// mPDF 5.3.42	VISIBILITY
			if($pb['visibility']!='visible') {
				if($pb['visibility']=='printonly') 
					$s .= '/OC /OC1 BDC'."\n";
				else if($pb['visibility']=='screenonly')
					$s .= '/OC /OC2 BDC'."\n";
				else if($pb['visibility']=='hidden')
					$s .= '/OC /OC3 BDC'."\n";
			}
			// mPDF 5.3.A1
			// Box shadow
			if (isset($pb['shadow']) && $pb['shadow']) { $s .= $pb['shadow']."\n"; }
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= $pb['clippath']."\n"; }
			$s .= 'q '.$this->SetFColor($pb['col'], true)."\n";
			// mPDF 5.3.74
			if ($pb['col']{0}==5) {	// RGBa
				$s .= $this->SetAlpha(ord($pb['col']{4})/100, 'Normal', true, 'F')."\n"; 
			}
			else if ($pb['col']{0}==6) {	// CMYKa
				$s .= $this->SetAlpha(ord($pb['col']{5})/100, 'Normal', true, 'F')."\n";
			}
			$s .= sprintf('%.3F %.3F %.3F %.3F re f Q',$pb['x']*_MPDFK,($this->h-$pb['y'])*_MPDFK,$pb['w']*_MPDFK,-$pb['h']*_MPDFK)."\n";
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= 'Q'."\n"; }
			// mPDF 5.3.42
			if($pb['visibility']!='visible')
				$s .= 'EMC'."\n";
		  }
		}
/*-- BACKGROUNDS --*/
		foreach ($pbs AS $pb) {
		 // mPDF 5.3.42	VISIBILITY
		 if($pb['visibility']!='visible') {
				if($pb['visibility']=='printonly') 
					$s .= '/OC /OC1 BDC'."\n";
				else if($pb['visibility']=='screenonly')
					$s .= '/OC /OC2 BDC'."\n";
				else if($pb['visibility']=='hidden')
					$s .= '/OC /OC3 BDC'."\n";
		 }
	 	 if (isset($pb['gradient']) && $pb['gradient']) {
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= $pb['clippath']."\n"; }
			$s .= $this->grad->Gradient($pb['x'], $pb['y'], $pb['w'], $pb['h'], $pb['gradtype'], $pb['stops'], $pb['colorspace'], $pb['coords'], $pb['extend'], true);
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= 'Q'."\n"; }
		  }
		  else if (isset($pb['image_id']) && $pb['image_id']) {	// Background pattern
			$pb['y'] -= $adjustmenty; 
			$pb['h'] += $adjustmenty; 
			$n = count($this->patterns)+1;
			list($orig_w, $orig_h, $x_repeat, $y_repeat) = $this->_resizeBackgroundImage($pb['orig_w'], $pb['orig_h'], $pb['w'], $pb['h'], $pb['resize'], $pb['x_repeat'], $pb['y_repeat']);
			$this->patterns[$n] = array('x'=>$pb['x'], 'y'=>$pb['y'], 'w'=>$pb['w'], 'h'=>$pb['h'], 'pgh'=>$this->h, 'image_id'=>$pb['image_id'], 'orig_w'=>$orig_w, 'orig_h'=>$orig_h, 'x_pos'=>$pb['x_pos'], 'y_pos'=>$pb['y_pos'], 'x_repeat'=>$x_repeat, 'y_repeat'=>$y_repeat, 'itype'=>$pb['itype']);
			$x = $pb['x']*_MPDFK;
			$y = ($this->h - $pb['y'])*_MPDFK;
			$w = $pb['w']*_MPDFK;
			$h = -$pb['h']*_MPDFK;
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= $pb['clippath']."\n"; }
			if ($this->writingHTMLfooter || $this->writingHTMLheader) {
				$iw = $pb['orig_w']/_MPDFK;
				$ih = $pb['orig_h']/_MPDFK;
				$w = $pb['w'];
				$h = $pb['h'];
				$x0 = $pb['x'];
				$y0 = $pb['y'];

				// Number to repeat
				if ($pb['x_repeat']) { $nx = ceil($w/$iw); } 
				else { $nx = 1; }
				if ($pb['y_repeat']) { $ny = ceil($h/$ih); }
				else { $ny = 1; }

				$x_pos = $pb['x_pos'];
				if (stristr($x_pos ,'%') ) { 
					$x_pos += 0; 
					$x_pos /= 100; 
					$x_pos = ($w * $x_pos) - ($iw * $x_pos);
				}
				$y_pos = $pb['y_pos'];
				if (stristr($y_pos ,'%') ) { 
					$y_pos += 0; 
					$y_pos /= 100; 
					$y_pos = ($h * $y_pos) - ($ih * $y_pos);
				}
				if ($nx>1) {
					while($x_pos>0) { $x_pos -= $iw; }
				}
				if ($ny>1) {
					while($y_pos>0) { $y_pos -= $ih; }
				}
				for($xi=0;$xi<$nx;$xi++) {
				  for($yi=0;$yi<$ny;$yi++) {
					$x = $x0 + $x_pos + ($iw*$xi);
					$y = $y0 + $y_pos + ($ih*$yi);
					if ($pb['opacity']>0 && $pb['opacity']<1) { $opac = $this->SetAlpha($pb['opacity'],'Normal',true); }
					else { $opac = ''; }
					$s .= sprintf("q %s %.3F 0 0 %.3F %.3F %.3F cm /I%d Do Q", $opac,$iw*_MPDFK,$ih*_MPDFK,$x*_MPDFK,($this->h-($y+$ih))*_MPDFK,$pb['image_id']) ."\n";
				  }
				}
			}
			else {
				// mPDF 5.3.12
				if (($pb['opacity']>0 || $pb['opacity']==='0') && $pb['opacity']<1) { $opac = $this->SetAlpha($pb['opacity'],'Normal',true); }
				else { $opac = ''; }
				$s .= sprintf('q /Pattern cs /P%d scn %s %.3F %.3F %.3F %.3F re f Q', $n, $opac, $x, $y, $w, $h) ."\n";
			}
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= 'Q'."\n"; }
		  }
		 // mPDF 5.3.42	VISIBILITY
		 if($pb['visibility']!='visible')
			$s .= 'EMC'."\n";

		}
/*-- END BACKGROUNDS --*/
	}
	return $s;
}

function PrintTableBackgrounds($adjustmenty=0) {
	$s = '';
/*-- BACKGROUNDS --*/
	ksort($this->tableBackgrounds);
	foreach($this->tableBackgrounds AS $bl=>$pbs) {
		foreach ($pbs AS $pb) {
	 	 if ((!isset($pb['gradient']) || !$pb['gradient']) && (!isset($pb['image_id']) || !$pb['image_id'])) {
			$s .= 'q '.$this->SetFColor($pb['col'], true)."\n";	// mPDF 5.3.68
			// mPDF 5.3.68
			// mPDF 5.3.74
			if ($pb['col']{0}==5) {	// RGBa
				$s .= $this->SetAlpha(ord($pb['col']{4})/100, 'Normal', true, 'F')."\n"; 
			}
			else if ($pb['col']{0}==6) {	// CMYKa
				$s .= $this->SetAlpha(ord($pb['col']{5})/100, 'Normal', true, 'F')."\n";
			}
			$s .= sprintf('%.3F %.3F %.3F %.3F re %s Q',$pb['x']*_MPDFK,($this->h-$pb['y'])*_MPDFK,$pb['w']*_MPDFK,-$pb['h']*_MPDFK,'f')."\n";	// mPDF 5.3.68
		  }
	 	 if (isset($pb['gradient']) && $pb['gradient']) {
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= $pb['clippath']."\n"; }
			$s .= $this->grad->Gradient($pb['x'], $pb['y'], $pb['w'], $pb['h'], $pb['gradtype'], $pb['stops'], $pb['colorspace'], $pb['coords'], $pb['extend'], true);
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= 'Q'."\n"; }
		  }
		  if (isset($pb['image_id']) && $pb['image_id']) {	// Background pattern
			$pb['y'] -= $adjustmenty; 
			$pb['h'] += $adjustmenty; 
			$n = count($this->patterns)+1;
			list($orig_w, $orig_h, $x_repeat, $y_repeat) = $this->_resizeBackgroundImage($pb['orig_w'], $pb['orig_h'], $pb['w'], $pb['h'], $pb['resize'], $pb['x_repeat'], $pb['y_repeat']);
			$this->patterns[$n] = array('x'=>$pb['x'], 'y'=>$pb['y'], 'w'=>$pb['w'], 'h'=>$pb['h'], 'pgh'=>$this->h, 'image_id'=>$pb['image_id'], 'orig_w'=>$orig_w, 'orig_h'=>$orig_h, 'x_pos'=>$pb['x_pos'], 'y_pos'=>$pb['y_pos'], 'x_repeat'=>$x_repeat, 'y_repeat'=>$y_repeat, 'itype'=>$pb['itype']);
			$x = $pb['x']*_MPDFK;
			$y = ($this->h - $pb['y'])*_MPDFK;
			$w = $pb['w']*_MPDFK;
			$h = -$pb['h']*_MPDFK;
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= $pb['clippath']."\n"; }
			if ($pb['opacity']>0 && $pb['opacity']<1) { $opac = $this->SetAlpha($pb['opacity'],'Normal',true); }
			else { $opac = ''; }
			$s .= sprintf('q /Pattern cs /P%d scn %s %.3F %.3F %.3F %.3F re f Q', $n, $opac, $x, $y, $w, $h) ."\n";
			if (isset($pb['clippath']) && $pb['clippath']) { $s .= 'Q'."\n"; }
		  }
		}
	}
/*-- END BACKGROUNDS --*/
	return $s;
}


// Depracated - can use AddPage for all
function AddPages($orientation='',$condition='', $resetpagenum='', $pagenumstyle='', $suppress='',$mgl='',$mgr='',$mgt='',$mgb='',$mgh='',$mgf='',$ohname='',$ehname='',$ofname='',$efname='',$ohvalue=0,$ehvalue=0,$ofvalue=0,$efvalue=0,$pagesel='',$newformat='')
{
	$this->AddPage($orientation,$condition,$resetpagenum, $pagenumstyle, $suppress,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf, $ohname, $ehname, $ofname, $efname, $ohvalue, $ehvalue, $ofvalue, $efvalue,$pagesel,$newformat); 
}


function AddPageByArray($a) {
	if (!is_array($a)) { $a = array(); }
	// mPDF 5.3.26
	$orientation = (isset($a['orientation']) ? $a['orientation'] : '');
	$condition = (isset($a['condition']) ? $a['condition'] : (isset($a['type']) ? $a['type'] : ''));
	$resetpagenum = (isset($a['resetpagenum']) ? $a['resetpagenum'] : '');
	$pagenumstyle = (isset($a['pagenumstyle']) ? $a['pagenumstyle'] : '');
	$suppress = (isset($a['suppress']) ? $a['suppress'] : '');
	$mgl = (isset($a['mgl']) ? $a['mgl'] : (isset($a['margin-left']) ? $a['margin-left'] : ''));
	$mgr = (isset($a['mgr']) ? $a['mgr'] : (isset($a['margin-right']) ? $a['margin-right'] : ''));
	$mgt = (isset($a['mgt']) ? $a['mgt'] : (isset($a['margin-top']) ? $a['margin-top'] : ''));
	$mgb = (isset($a['mgb']) ? $a['mgb'] : (isset($a['margin-bottom']) ? $a['margin-bottom'] : ''));
	$mgh = (isset($a['mgh']) ? $a['mgh'] : (isset($a['margin-header']) ? $a['margin-header'] : ''));
	$mgf = (isset($a['mgf']) ? $a['mgf'] : (isset($a['margin-footer']) ? $a['margin-footer'] : ''));
	$ohname = (isset($a['ohname']) ? $a['ohname'] : (isset($a['odd-header-name']) ? $a['odd-header-name'] : ''));
	$ehname = (isset($a['ehname']) ? $a['ehname'] : (isset($a['even-header-name']) ? $a['even-header-name'] : ''));
	$ofname = (isset($a['ofname']) ? $a['ofname'] : (isset($a['odd-footer-name']) ? $a['odd-footer-name'] : ''));
	$efname = (isset($a['efname']) ? $a['efname'] : (isset($a['even-footer-name']) ? $a['even-footer-name'] : ''));
	$ohvalue = (isset($a['ohvalue']) ? $a['ohvalue'] : (isset($a['odd-header-value']) ? $a['odd-header-value'] : 0));
	$ehvalue = (isset($a['ehvalue']) ? $a['ehvalue'] : (isset($a['even-header-value']) ? $a['even-header-value'] : 0));
	$ofvalue = (isset($a['ofvalue']) ? $a['ofvalue'] : (isset($a['odd-footer-value']) ? $a['odd-footer-value'] : 0));
	$efvalue = (isset($a['efvalue']) ? $a['efvalue'] : (isset($a['even-footer-value']) ? $a['even-footer-value'] : 0));
	$pagesel = (isset($a['pagesel']) ? $a['pagesel'] : (isset($a['pageselector']) ? $a['pageselector'] : ''));
	$newformat = (isset($a['newformat']) ? $a['newformat'] : (isset($a['sheet-size']) ? $a['sheet-size'] : ''));

	$this->AddPage($orientation,$condition,$resetpagenum, $pagenumstyle, $suppress,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf, $ohname, $ehname, $ofname, $efname, $ohvalue, $ehvalue, $ofvalue, $efvalue,$pagesel,$newformat);

}




function AddPage($orientation='',$condition='', $resetpagenum='', $pagenumstyle='', $suppress='',$mgl='',$mgr='',$mgt='',$mgb='',$mgh='',$mgf='',$ohname='',$ehname='',$ofname='',$efname='',$ohvalue=0,$ehvalue=0,$ofvalue=0,$efvalue=0,$pagesel='',$newformat='')
{
/*-- CSS-FLOAT --*/
	// Float DIV
	// Cannot do with columns on, or if any change in page orientation/margins etc.
	// If next page already exists - i.e background /headers and footers already written
	if ($this->state > 0 && $this->page < count($this->pages)) {
		$bak_cml = $this->cMarginL;
		$bak_cmr = $this->cMarginR; 
		$bak_dw = $this->divwidth;
		// Paint Div Border if necessary
   		if ($this->blklvl > 0) {
			$save_tr = $this->table_rotate;	// *TABLES*
			$this->table_rotate = 0;	// *TABLES*
			if ($this->y == $this->blk[$this->blklvl]['y0']) {  $this->blk[$this->blklvl]['startpage']++; }
			if (($this->y > $this->blk[$this->blklvl]['y0']) || $this->flowingBlockAttr['is_table'] ) { $toplvl = $this->blklvl; }
			else { $toplvl = $this->blklvl-1; }
			$sy = $this->y;
			for ($bl=1;$bl<=$toplvl;$bl++) {
				$this->PaintDivBB('pagebottom',0,$bl);
			}
			$this->y = $sy;
			$this->table_rotate = $save_tr;	// *TABLES*
		}
		$s = $this->PrintPageBackgrounds();

		// Writes after the marker so not overwritten later by page background etc.
		$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', '\\1'."\n".$s."\n", $this->pages[$this->page]);
		$this->pageBackgrounds = array();
		$family=$this->FontFamily;
		$style=$this->FontStyle.($this->U ? 'U' : '').($this->S ? 'S' : '');
		$size=$this->FontSizePt;
		$lw=$this->LineWidth;
		$dc=$this->DrawColor;
		$fc=$this->FillColor;
		$tc=$this->TextColor;
		$cf=$this->ColorFlag;

		$this->printfloatbuffer();

		//Move to next page
		$this->page++;
		$this->ResetMargins();
		$this->SetAutoPageBreak($this->autoPageBreak,$this->bMargin);
		$this->x=$this->lMargin;
		$this->y=$this->tMargin;
		$this->FontFamily='';
		$this->_out('2 J');
		$this->LineWidth=$lw;
		$this->_out(sprintf('%.3F w',$lw*_MPDFK));
		if($family)	$this->SetFont($family,$style,$size,true,true);
		$this->DrawColor=$dc;
		if($dc!=$this->defDrawColor) $this->_out($dc);
		$this->FillColor=$fc;
		if($fc!=$this->defFillColor) $this->_out($fc);
		$this->TextColor=$tc;
		$this->ColorFlag=$cf;
		for($bl=1;$bl<=$this->blklvl;$bl++) {
			$this->blk[$bl]['y0'] = $this->y;
			// Don't correct more than once for background DIV containing a Float
			if (!isset($this->blk[$bl]['marginCorrected'][$this->page])) { $this->blk[$bl]['x0'] += $this->MarginCorrection; }
			$this->blk[$bl]['marginCorrected'][$this->page] = true; 
		}
		$this->cMarginL = $bak_cml;
		$this->cMarginR = $bak_cmr;
		$this->divwidth = $bak_dw;
		return '';
	}
/*-- END CSS-FLOAT --*/

	//Start a new page
	if($this->state==0) $this->Open();

	$bak_cml = $this->cMarginL;
	$bak_cmr = $this->cMarginR; 
	$bak_dw = $this->divwidth;


	$bak_lh = $this->lineheight;

	$orientation = substr(strtoupper($orientation),0,1);
	$condition = strtoupper($condition);


	if ($condition == 'NEXT-EVEN') {	// always adds at least one new page to create an Even page
	   if (!$this->mirrorMargins) { $condition = ''; }
	   else { 
		if ($pagesel) { $pbch = $pagesel; $pagesel = ''; }	// *CSS-PAGE*
		else { $pbch = false; }	// *CSS-PAGE*
		$this->AddPage($this->CurOrientation,'O'); 
		if ($pbch ) { $pagesel = $pbch; }	// *CSS-PAGE*
		$condition = ''; 
	   }
	}
	if ($condition == 'NEXT-ODD') {	// always adds at least one new page to create an Odd page
	   if (!$this->mirrorMargins) { $condition = ''; }
	   else { 
		if ($pagesel) { $pbch = $pagesel; $pagesel = ''; }	// *CSS-PAGE*
		else { $pbch = false; }	// *CSS-PAGE*
		$this->AddPage($this->CurOrientation,'E'); 
		if ($pbch ) { $pagesel = $pbch; }	// *CSS-PAGE*
		$condition = ''; 
	   }
	}


	if ($condition == 'E') {	// only adds new page if needed to create an Even page
	   if (!$this->mirrorMargins || ($this->page)%2==0) { return false; }
	}
	if ($condition == 'O') {	// only adds new page if needed to create an Odd page
	   if (!$this->mirrorMargins || ($this->page)%2==1) { return false; }
	}

	if ($resetpagenum || $pagenumstyle || $suppress) {
		$this->PageNumSubstitutions[] = array('from'=>($this->page+1), 'reset'=> $resetpagenum, 'type'=>$pagenumstyle, 'suppress'=>$suppress);
	}


	$save_tr = $this->table_rotate;	// *TABLES*
	$this->table_rotate = 0;	// *TABLES*
	$save_kwt = $this->kwt;
	$this->kwt = 0;

	// Paint Div Border if necessary
   	//PAINTS BACKGROUND COLOUR OR BORDERS for DIV - DISABLED FOR COLUMNS (cf. AcceptPageBreak) AT PRESENT in ->PaintDivBB
   	if (!$this->ColActive && $this->blklvl > 0) {
		if (isset($this->blk[$this->blklvl]['y0']) && $this->y == $this->blk[$this->blklvl]['y0']) {  
			if (isset($this->blk[$this->blklvl]['startpage'])) { $this->blk[$this->blklvl]['startpage']++; }
			else { $this->blk[$this->blklvl]['startpage'] = 1; }
		}
		if ((isset($this->blk[$this->blklvl]['y0']) && $this->y > $this->blk[$this->blklvl]['y0']) || $this->flowingBlockAttr['is_table'] ) { $toplvl = $this->blklvl; }
		else { $toplvl = $this->blklvl-1; }
		$sy = $this->y;
		for ($bl=1;$bl<=$toplvl;$bl++) {
			$this->PaintDivBB('pagebottom',0,$bl);
		}
		$this->y = $sy;
		// RESET block y0 and x0 - see below
	}

	// BODY Backgrounds
	if ($this->page > 0) {
		$s = '';
		$s .= $this->PrintBodyBackgrounds();

		$s .= $this->PrintPageBackgrounds();
		$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', "\n".$s."\n".'\\1', $this->pages[$this->page]);
		$this->pageBackgrounds = array();
	}

	$save_kt = $this->keep_block_together;
	$this->keep_block_together = 0;

	$save_cols = false;
/*-- COLUMNS --*/
	if ($this->ColActive) {
		$save_cols = true;
		$save_nbcol = $this->NbCol;	// other values of gap and vAlign will not change by setting Columns off
		$this->SetColumns(0);
	}
/*-- END COLUMNS --*/

	// mPDF 5.3.41
	$save_vis = $this->visibility;
	if($this->visibility!='visible')
		$this->SetVisibility('visible');

	$family=$this->FontFamily;
	$style=$this->FontStyle.($this->U ? 'U' : '').($this->S ? 'S' : '');
	$size=$this->FontSizePt;
	$this->ColumnAdjust = true;	// enables column height adjustment for the page
	$lw=$this->LineWidth;
	$dc=$this->DrawColor;
	$fc=$this->FillColor;
	$tc=$this->TextColor;
	$cf=$this->ColorFlag;
	if($this->page>0)
	{
		//Page footer
		$this->InFooter=true;

		$this->Reset();
		$this->pageoutput[$this->page] = array();

		$this->Footer();
		//Close page
		$this->_endpage();
	}


	//Start new page
	$this->_beginpage($orientation,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$ohname,$ehname,$ofname,$efname,$ohvalue,$ehvalue,$ofvalue,$efvalue,$pagesel,$newformat);
	if ($this->docTemplate) {
		$pagecount = $this->SetSourceFile($this->docTemplate);
		if (($this->page - $this->docTemplateStart) > $pagecount) {
			if ($this->docTemplateContinue) { 
				$tplIdx = $this->ImportPage($pagecount);
				$this->UseTemplate($tplIdx);
			}
		}
		else {
			$tplIdx = $this->ImportPage(($this->page - $this->docTemplateStart));
			$this->UseTemplate($tplIdx);
		}
	}
	if ($this->pageTemplate) {
		$this->UseTemplate($this->pageTemplate);
	}

	// Tiling Patterns
	$this->_out('___PAGE___START'.date('jY'));
	$this->_out('___BACKGROUND___PATTERNS'.date('jY'));
	$this->_out('___HEADER___MARKER'.date('jY'));
	$this->pageBackgrounds = array();

	//Set line cap style to square
	$this->SetLineCap(2);
	//Set line width
	$this->LineWidth=$lw;
	$this->_out(sprintf('%.3F w',$lw*_MPDFK));
	//Set font
	if($family)	$this->SetFont($family,$style,$size,true,true);	// forces write
	//Set colors
	$this->DrawColor=$dc;
	if($dc!=$this->defDrawColor) $this->_out($dc);
	$this->FillColor=$fc;
	if($fc!=$this->defFillColor) $this->_out($fc);
	$this->TextColor=$tc;
	$this->ColorFlag=$cf;

	//Page header
	$this->Header();

	//Restore line width
	if($this->LineWidth!=$lw)
	{
		$this->LineWidth=$lw;
		$this->_out(sprintf('%.3F w',$lw*_MPDFK));
	}
	//Restore font
	if($family)	$this->SetFont($family,$style,$size,true,true);	// forces write
	//Restore colors
	if($this->DrawColor!=$dc)
	{
		$this->DrawColor=$dc;
		$this->_out($dc);
	}
	if($this->FillColor!=$fc)
	{
		$this->FillColor=$fc;
		$this->_out($fc);
	}
	$this->TextColor=$tc;
	$this->ColorFlag=$cf;
 	$this->InFooter=false;

	// mPDF 5.3.41
	if($save_vis!='visible')
		$this->SetVisibility($save_vis);

/*-- COLUMNS --*/
	if ($save_cols) {
		// Restore columns
		$this->SetColumns($save_nbcol,$this->colvAlign,$this->ColGap);
	}
	if ($this->ColActive) { $this->SetCol(0); }
/*-- END COLUMNS --*/


   	//RESET BLOCK BORDER TOP
   	if (!$this->ColActive) {
		for($bl=1;$bl<=$this->blklvl;$bl++) {
			$this->blk[$bl]['y0'] = $this->y;
			if (isset($this->blk[$bl]['x0'])) { $this->blk[$bl]['x0'] += $this->MarginCorrection; }
			else { $this->blk[$bl]['x0'] = $this->MarginCorrection; }
			// Added mPDF 3.0 Float DIV
			$this->blk[$bl]['marginCorrected'][$this->page] = true; 
		}
	}


	$this->table_rotate = $save_tr;	// *TABLES*
	$this->kwt = $save_kwt;

	$this->keep_block_together = $save_kt ;

	$this->cMarginL = $bak_cml;
	$this->cMarginR = $bak_cmr;
	$this->divwidth = $bak_dw;

	$this->lineheight = $bak_lh;
}


function PageNo() {
	//Get current page number
	return $this->page;
}

function AddSpotColorsFromFile($file) {
	$colors = @file($file) or die("Cannot load spot colors file - ".$file);
	foreach($colors AS $sc) {
		list($name, $c, $m, $y, $k) = preg_split("/\t/",$sc);
		$c = intval($c);
		$m = intval($m);
		$y = intval($y);
		$k = intval($k);
		$this->AddSpotColor($name, $c, $m, $y, $k);
	}
}

function AddSpotColor($name, $c, $m, $y, $k) {
	$name = strtoupper(trim($name));
	if(!isset($this->spotColors[$name])) {
		$i=count($this->spotColors)+1;
		$this->spotColors[$name]=array('i'=>$i,'c'=>$c,'m'=>$m,'y'=>$y,'k'=>$k);
		$this->spotColorIDs[$i]=$name;
	}
}

function SetColor($col, $type='') {
	$out = '';
	// mPDF 5.3.74
	if ($col{0}==3 || $col{0}==5) {	// RGB / RGBa
		$out = sprintf('%.3F %.3F %.3F rg',ord($col{1})/255,ord($col{2})/255,ord($col{3})/255);
	}
	else if ($col{0}==1) {	// GRAYSCALE
		$out = sprintf('%.3F g',ord($col{1})/255);
	}
	else if ($col{0}==2) {	// SPOT COLOR
		$out = sprintf('/CS%d cs %.3F scn',ord($col{1}),ord($col{2})/100);
	}
	else if ($col{0}==4 || $col{0}==6) {	// CMYK / CMYKa
		$out = sprintf('%.3F %.3F %.3F %.3F k', ord($col{1})/100, ord($col{2})/100, ord($col{3})/100, ord($col{4})/100);
	}
	if ($type=='Draw') { $out = strtoupper($out); }	// e.g. rg => RG
	else if ($type=='CodeOnly') { $out = preg_replace('/\s(rg|g|k)/','',$out); }
	return $out; 
}


function SetDColor($col, $return=false) {
	$out = $this->SetColor($col, 'Draw');
	if ($return) { return $out; }
	if ($out=='') { return ''; }
	$this->DrawColor = $out;
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['DrawColor']) && $this->pageoutput[$this->page]['DrawColor'] != $this->DrawColor) || !isset($this->pageoutput[$this->page]['DrawColor']) || $this->keep_block_together)) { $this->_out($this->DrawColor); }
	$this->pageoutput[$this->page]['DrawColor'] = $this->DrawColor;
}

function SetFColor($col, $return=false) {
	$out = $this->SetColor($col, 'Fill');
	if ($return) { return $out; }
	if ($out=='') { return ''; }
	$this->FillColor = $out;
	$this->ColorFlag = ($out != $this->TextColor);
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['FillColor']) && $this->pageoutput[$this->page]['FillColor'] != $this->FillColor) || !isset($this->pageoutput[$this->page]['FillColor']) || $this->keep_block_together)) { $this->_out($this->FillColor); }
	$this->pageoutput[$this->page]['FillColor'] = $this->FillColor;
}

function SetTColor($col, $return=false) {
	$out = $this->SetColor($col, 'Text');
	if ($return) { return $out; }
	if ($out=='') { return ''; }
	$this->TextColor = $out;
	$this->ColorFlag = ($this->FillColor != $out);
} 


function SetDrawColor($r,$g=-1,$b=-1,$col4=-1, $return=false) {
	//Set color for all stroking operations
	$col = array();
	if(($r==0 and $g==0 and $b==0 && $col4 == -1) or $g==-1) { $col = $this->ConvertColor($r); }
	else if ($col4 == -1) { $col = $this->ConvertColor('rgb('.$r.','.$g.','.$b.')'); }
	else { $col = $this->ConvertColor('cmyk('.$r.','.$g.','.$b.','.$col4.')'); }
	$out = $this->SetDColor($col, $return);
	return $out;
}

function SetFillColor($r,$g=-1,$b=-1,$col4=-1, $return=false) {
	//Set color for all filling operations
	$col = array();
	if(($r==0 and $g==0 and $b==0 && $col4 == -1) or $g==-1) { $col = $this->ConvertColor($r); }
	else if ($col4 == -1) { $col = $this->ConvertColor('rgb('.$r.','.$g.','.$b.')'); }
	else { $col = $this->ConvertColor('cmyk('.$r.','.$g.','.$b.','.$col4.')'); }
	$out = $this->SetFColor($col, $return);
	return $out;
}

function SetTextColor($r,$g=-1,$b=-1,$col4=-1, $return=false) {
	//Set color for text
	$col = array();
	if(($r==0 and $g==0 and $b==0 && $col4 == -1) or $g==-1) { $col = $this->ConvertColor($r); }
	else if ($col4 == -1) { $col = $this->ConvertColor('rgb('.$r.','.$g.','.$b.')'); }
	else { $col = $this->ConvertColor('cmyk('.$r.','.$g.','.$b.','.$col4.')'); }
	$out = $this->SetTColor($col, $return);
	return $out;
}

function _getCharWidth(&$cw, $u, $isdef=true) {
	if ($u==0) { $w = false; }
	else { $w = (ord($cw[$u*2]) << 8) + ord($cw[$u*2+1]); }
	if ($w == 65535) { return 0; }
	else if ($w) { return $w; }
	else if ($isdef) { return false; }
	else { return 0; }
}

function _charDefined(&$cw, $u) {
	if ($u==0) { return false; }
	$w = (ord($cw[$u*2]) << 8) + ord($cw[$u*2+1]);
	if ($w) { return true; }
	else { return false; }
}

// mPDF 5.3.04
function GetCharWidthCore($c) {
	//Get width of a single character in the current Core font
	$c = (string)$c;
	$w = 0;
	// Soft Hyphens chr(173)
	if ($c == chr(173) && $this->FontFamily!='csymbol' && $this->FontFamily!='czapfdingbats') { 
		return 0;	// mPDF 5.3.07
	}
	else if ($this->S && isset($this->upperCase[ord($c)])) { 
		$charw = $this->CurrentFont['cw'][chr($this->upperCase[ord($c)])];
		if ($charw!==false) { 
			$charw = $charw*$this->smCapsScale * $this->smCapsStretch/100;
			$w+=$charw; 
		}
	}
	else if (isset($this->CurrentFont['cw'][$c])) { 
		$w += $this->CurrentFont['cw'][$c]; 
	} 
	else if (isset($this->CurrentFont['cw'][ord($c)])) { 
		$w += $this->CurrentFont['cw'][ord($c)]; 
	}
	$w *=  ($this->FontSize/ 1000);
	if ($this->minwSpacing || $this->fixedlSpacing) {
		if ($c==' ') $nb_spaces = 1;
		else $nb_spaces = 0;
		$w += $this->fixedlSpacing + ($nb_spaces * $this->minwSpacing);
	}
	return ($w);
}

// mPDF 5.3.04
function GetCharWidthNonCore($c, $addSubset=true) {
	//Get width of a single character in the current Non-Core font
	$c = (string)$c;
	$w = 0;
	$unicode = $this->UTF8StringToArray($c, $addSubset);	// mPDF 5.3.06
	$char = $unicode[0];
/*-- CJK-FONTS --*/
	if ($this->CurrentFont['type'] == 'Type0') {	// CJK Adobe fonts
			if ($char == 173) { return 0; }	// Soft Hyphens	// mPDF 5.3.07
			elseif (isset($this->CurrentFont['cw'][$char])) { $w+=$this->CurrentFont['cw'][$char]; } 
			elseif(isset($this->CurrentFont['MissingWidth'])) { $w += $this->CurrentFont['MissingWidth']; }
			else { $w += 500; }
	}
	else { 
/*-- END CJK-FONTS --*/
			if ($char == 173) { return 0; }	// Soft Hyphens	// mPDF 5.3.07
			else if ($this->S && isset($this->upperCase[$char])) {
				$charw = $this->_getCharWidth($this->CurrentFont['cw'],$this->upperCase[$char]);
				if ($charw!==false) { 
					$charw = $charw*$this->smCapsScale * $this->smCapsStretch/100;
					$w+=$charw; 
				}
				elseif(isset($this->CurrentFont['desc']['MissingWidth'])) { $w += $this->CurrentFont['desc']['MissingWidth']; }
				elseif(isset($this->CurrentFont['MissingWidth'])) { $w += $this->CurrentFont['MissingWidth']; }
				else { $w += 500; }
			}
			else {
				$charw = $this->_getCharWidth($this->CurrentFont['cw'],$char);
				if ($charw!==false) { $w+=$charw; }
				elseif(isset($this->CurrentFont['desc']['MissingWidth'])) { $w += $this->CurrentFont['desc']['MissingWidth']; }
				elseif(isset($this->CurrentFont['MissingWidth'])) { $w += $this->CurrentFont['MissingWidth']; }
				else { $w += 500; }
			}
	}	// *CJK-FONTS*
	$w *=  ($this->FontSize/ 1000);
	if ($this->minwSpacing || $this->fixedlSpacing) {
		if ($c==' ') $nb_spaces = 1;
		else $nb_spaces = 0;
		$w += $this->fixedlSpacing + ($nb_spaces * $this->minwSpacing);
	}
	return ($w);
}


// mPDF 5.3.04
function GetCharWidth($c, $addSubset=true) {
	if (!$this->usingCoreFont) {
		return $this->GetCharWidthNonCore($c, $addSubset);
	} 
	else {
		return $this->GetCharWidthCore($c);
	}
}

function GetStringWidth($s, $addSubset=true) {
			//Get width of a string in the current font
			$s = (string)$s;
			$cw = &$this->CurrentFont['cw'];
			$w = 0;
			$kerning = 0;
			$lastchar = 0;
			// mPDF 5.3.04
			$nb_carac = 0;
			$nb_spaces = 0;
			// mPDF ITERATION
			if ($this->iterationCounter) $s = preg_replace('/{iteration ([a-zA-Z0-9_]+)}/', '\\1', $s);

			if (!$this->usingCoreFont) {
				// mPDF 5.3.04
			      $s = str_replace("\xc2\xad",'',$s ); 
				$unicode = $this->UTF8StringToArray($s, $addSubset);
				// mPDF 5.3.04
				if ($this->minwSpacing || $this->fixedlSpacing) {
					$nb_carac = count($unicode);  
					$nb_spaces = mb_substr_count($s,' ', $this->mb_enc);  
				}
/*-- CJK-FONTS --*/
				if ($this->CurrentFont['type'] == 'Type0') {	// CJK Adobe fonts
					foreach($unicode as $char) {
						if (isset($cw[$char])) { $w+=$cw[$char]; } 
						elseif(isset($this->CurrentFont['MissingWidth'])) { $w += $this->CurrentFont['MissingWidth']; }
						else { $w += 500; }
					}
				}
				else { 
/*-- END CJK-FONTS --*/
					foreach($unicode as $char) {
						if ($this->S && isset($this->upperCase[$char])) {
							$charw = $this->_getCharWidth($cw,$this->upperCase[$char]);
							if ($charw!==false) { 
								$charw = $charw*$this->smCapsScale * $this->smCapsStretch/100;
								$w+=$charw; 
							}
							elseif(isset($this->CurrentFont['desc']['MissingWidth'])) { $w += $this->CurrentFont['desc']['MissingWidth']; }
							elseif(isset($this->CurrentFont['MissingWidth'])) { $w += $this->CurrentFont['MissingWidth']; }
							else { $w += 500; }
						}
						else {
							$charw = $this->_getCharWidth($cw,$char);
							if ($charw!==false) { $w+=$charw; }
							elseif(isset($this->CurrentFont['desc']['MissingWidth'])) { $w += $this->CurrentFont['desc']['MissingWidth']; }
							elseif(isset($this->CurrentFont['MissingWidth'])) { $w += $this->CurrentFont['MissingWidth']; }
							else { $w += 500; }
							if ($this->kerning && $this->useKerning && $lastchar) {
								if (isset($this->CurrentFont['kerninfo'][$lastchar][$char])) { 
									$kerning += $this->CurrentFont['kerninfo'][$lastchar][$char]; 
								}
							}
							$lastchar = $char;
						}
					}
				}	// *CJK-FONTS*

			} 
			else {
				// mPDF 5.3.04
				if ($this->FontFamily!='csymbol' && $this->FontFamily!='czapfdingbats') { 
			      	$s = str_replace(chr(173),'',$s ); 
				}
				$nb_carac = $l = strlen($s);
				// mPDF 5.3.04
				if ($this->minwSpacing || $this->fixedlSpacing) {
					$nb_spaces = substr_count($s,' ');  
				}
				for($i=0; $i<$l; $i++) {
					if ($this->S && isset($this->upperCase[ord($s[$i])])) { 
						$charw = $cw[chr($this->upperCase[ord($s[$i])])];
						if ($charw!==false) { 
							$charw = $charw*$this->smCapsScale * $this->smCapsStretch/100;
							$w+=$charw; 
						}
					}
					else if (isset($cw[$s[$i]])) { 
						$w += $cw[$s[$i]]; 
					} 
					else if (isset($cw[ord($s[$i])])) { 
						$w += $cw[ord($s[$i])]; 
					}
					if ($this->kerning && $this->useKerning && $i>0) {	// mPDF 5.3.04
						if (isset($this->CurrentFont['kerninfo'][$s[($i-1)]][$s[$i]])) { 
							$kerning += $this->CurrentFont['kerninfo'][$s[($i-1)]][$s[$i]]; 
						}
					}
				}
			}
			unset($cw);
			if ($this->kerning && $this->useKerning) { $w += $kerning; }
			$w *=  ($this->FontSize/ 1000);
			// mPDF 5.3.04
			$w += (($nb_carac + $nb_spaces) * $this->fixedlSpacing) + ($nb_spaces * $this->minwSpacing);
			return ($w);
}

function SetLineWidth($width) {
	//Set line width
	$this->LineWidth=$width;
	$lwout = (sprintf('%.3F w',$width*_MPDFK));
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['LineWidth']) && $this->pageoutput[$this->page]['LineWidth'] != $lwout) || !isset($this->pageoutput[$this->page]['LineWidth']) || $this->keep_block_together)) {
		 $this->_out($lwout); 
	}
	$this->pageoutput[$this->page]['LineWidth'] = $lwout;
}

function Line($x1,$y1,$x2,$y2) {
	//Draw a line
	$this->_out(sprintf('%.3F %.3F m %.3F %.3F l S',$x1*_MPDFK,($this->h-$y1)*_MPDFK,$x2*_MPDFK,($this->h-$y2)*_MPDFK));
}

function Arrow($x1,$y1,$x2,$y2,$headsize=3,$fill='B',$angle=25) {
  //F == fill //S == stroke //B == stroke and fill 
  // angle = splay of arrowhead - 1 - 89 degrees
  if($fill=='F')	$fill='f';
  elseif($fill=='FD' or $fill=='DF' or $fill=='B') $fill='B';
  else $fill='S';
  $a = atan2(($y2-$y1),($x2-$x1));
  $b = $a + deg2rad($angle);
  $c = $a - deg2rad($angle);
  $x3 = $x2 - ($headsize* cos($b));
  $y3 = $this->h-($y2 - ($headsize* sin($b)));
  $x4 = $x2 - ($headsize* cos($c));
  $y4 = $this->h-($y2 - ($headsize* sin($c)));

  $x5 = $x3-($x3-$x4)/2;	// mid point of base of arrowhead - to join arrow line to
  $y5 = $y3-($y3-$y4)/2;

  $s = '';
  $s.=sprintf('%.3F %.3F m %.3F %.3F l S',$x1*_MPDFK,($this->h-$y1)*_MPDFK,$x5*_MPDFK,$y5*_MPDFK);
  $this->_out($s);

  $s = '';
  $s.=sprintf('%.3F %.3F m %.3F %.3F l %.3F %.3F l %.3F %.3F l %.3F %.3F l ',$x5*_MPDFK,$y5*_MPDFK,$x3*_MPDFK,$y3*_MPDFK,$x2*_MPDFK,($this->h-$y2)*_MPDFK,$x4*_MPDFK,$y4*_MPDFK,$x5*_MPDFK,$y5*_MPDFK);
  $s.=$fill;
  $this->_out($s);
}


function Rect($x,$y,$w,$h,$style='') {
	//Draw a rectangle
	if($style=='F')	$op='f';
	elseif($style=='FD' or $style=='DF') $op='B';
	else $op='S';
	$this->_out(sprintf('%.3F %.3F %.3F %.3F re %s',$x*_MPDFK,($this->h-$y)*_MPDFK,$w*_MPDFK,-$h*_MPDFK,$op));
}

function AddFont($family,$style='') {
	if(empty($family)) { return; }
	$family = strtolower($family);
	$style=strtoupper($style);
	$style=str_replace('U','',$style);
	if($style=='IB') $style='BI';
	$fontkey = $family.$style;
	// check if the font has been already added
	if(isset($this->fonts[$fontkey])) {
		return;
	}

/*-- CJK-FONTS --*/
	if (in_array($family,$this->available_CJK_fonts)) { 
		if (empty($this->Big5_widths)) { require(_MPDF_PATH . 'includes/CJKdata.php'); }
		$this->AddCJKFont($family);	// don't need to add style
		return; 
	}
/*-- END CJK-FONTS --*/

	if ($this->usingCoreFont) { die("mPDF Error - problem with Font management"); }

	$stylekey = $style;
	if (!$style) { $stylekey = 'R'; }

	if (!isset($this->fontdata[$family][$stylekey]) || !$this->fontdata[$family][$stylekey]) {
		die('mPDF Error - Font is not supported - '.$family.' '.$style);
	}

	$name = '';
	$originalsize = 0;
	$sip = false;
	$smp = false;
	$haskerninfo = false;
	$BMPselected = false;
	@include(_MPDF_TTFONTDATAPATH.$fontkey.'.mtx.php');

	$ttffile = '';
	if (defined('_MPDF_SYSTEM_TTFONTS')) {
		$ttffile = _MPDF_SYSTEM_TTFONTS.$this->fontdata[$family][$stylekey];
		if (!file_exists($ttffile)) { $ttffile = ''; }
	}
	if (!$ttffile) {
		$ttffile = _MPDF_TTFONTPATH.$this->fontdata[$family][$stylekey];
		if (!file_exists($ttffile)) { die("mPDF Error - cannot find TTF TrueType font file - ".$ttffile); }
	}
	$ttfstat = stat($ttffile);

	if (isset($this->fontdata[$family]['TTCfontID'][$stylekey])) { $TTCfontID = $this->fontdata[$family]['TTCfontID'][$stylekey]; }
	else { $TTCfontID = 0; }


	$BMPonly = false;
	if (in_array($family,$this->BMPonly)) { $BMPonly = true; }
	$regenerate = false;
	if ($BMPonly && !$BMPselected) { $regenerate = true; }
	else if (!$BMPonly && $BMPselected) { $regenerate = true; }
	if ($this->useKerning && !$haskerninfo) { $regenerate = true; }

	if (!isset($name) || $originalsize != $ttfstat['size'] || $regenerate) {
		$mqr=$this->_getMQR();
		if ($mqr) { set_magic_quotes_runtime(0); }
		if (!class_exists('TTFontFile', false)) { include(_MPDF_PATH .'classes/ttfontsuni.php'); }
		$ttf = new TTFontFile();
		$ttf->getMetrics($ttffile, $TTCfontID, $this->debugfonts, $BMPonly, $this->useKerning);
		$cw = $ttf->charWidths;
		$kerninfo = $ttf->kerninfo;
		$haskerninfo = true;
		$name = preg_replace('/[ ()]/','',$ttf->fullName);
		$sip = $ttf->sipset;
		$smp = $ttf->smpset;

		$desc= array('Ascent'=>round($ttf->ascent),
		'Descent'=>round($ttf->descent),
		'CapHeight'=>round($ttf->capHeight),
		'Flags'=>$ttf->flags,
		'FontBBox'=>'['.round($ttf->bbox[0])." ".round($ttf->bbox[1])." ".round($ttf->bbox[2])." ".round($ttf->bbox[3]).']',
		'ItalicAngle'=>$ttf->italicAngle,
		'StemV'=>round($ttf->stemV),
		'MissingWidth'=>round($ttf->defaultWidth));
		$panose = '';
		if (count($ttf->panose)) { $panose = $ttf->sFamilyClass.' '.$ttf->sFamilySubClass.' '.implode(' ',$ttf->panose); }
		$up = round($ttf->underlinePosition);
		$ut = round($ttf->underlineThickness);
		$originalsize = $ttfstat['size']+0;
		$type = 'TTF';
		//Generate metrics .php file
		$s='<?php	 	'."\n";
		$s.='$name=\''.$name."';\n";
		$s.='$type=\''.$type."';\n";
		$s.='$desc='.var_export($desc,true).";\n";
		$s.='$up='.$up.";\n";
		$s.='$ut='.$ut.";\n";
		$s.='$ttffile=\''.$ttffile."';\n";
		$s.='$TTCfontID=\''.$TTCfontID."';\n";
		$s.='$originalsize='.$originalsize.";\n";
		if ($sip) $s.='$sip=true;'."\n";
		else $s.='$sip=false;'."\n";
		if ($smp) $s.='$smp=true;'."\n";
		else $s.='$smp=false;'."\n";
		if ($BMPonly) $s.='$BMPselected=true;'."\n";
		else $s.='$BMPselected=false;'."\n";
		$s.='$fontkey=\''.$fontkey."';\n";
		$s.='$panose=\''.$panose."';\n";
		if ($this->useKerning) { 
			$s.='$kerninfo='.var_export($kerninfo,true).";\n"; 
			$s.='$haskerninfo=true;'."\n";
		}
		else $s.='$haskerninfo=false;'."\n";
		$s.="?>";
		if (is_writable(dirname(_MPDF_TTFONTDATAPATH.'x'))) {
			$fh = fopen(_MPDF_TTFONTDATAPATH.$fontkey.'.mtx.php',"w");
			fwrite($fh,$s,strlen($s));
			fclose($fh);
			$fh = fopen(_MPDF_TTFONTDATAPATH.$fontkey.'.cw.dat',"wb");
			fwrite($fh,$cw,strlen($cw));
			fclose($fh);
			@unlink(_MPDF_TTFONTDATAPATH.$fontkey.'.cgm');
			@unlink(_MPDF_TTFONTDATAPATH.$fontkey.'.z');
			@unlink(_MPDF_TTFONTDATAPATH.$fontkey.'.cw127.php');
			@unlink(_MPDF_TTFONTDATAPATH.$fontkey.'.cw');
		}
		else if ($this->debugfonts) { $this->Error('Cannot write to the font caching directory - '._MPDF_TTFONTDATAPATH); }
		unset($ttf);
		if ($mqr) { set_magic_quotes_runtime($mqr); }
	}
	else {
		$cw = @file_get_contents(_MPDF_TTFONTDATAPATH.$fontkey.'.cw.dat'); 
	}

	if (isset($this->fontdata[$family]['indic']) && $this->fontdata[$family]['indic']) { $indic = true; }
	else { $indic = false; }
	if (isset($this->fontdata[$family]['sip-ext']) && $this->fontdata[$family]['sip-ext']) { $sipext = $this->fontdata[$family]['sip-ext']; }
	else { $sipext = ''; }


	$i = count($this->fonts)+$this->extraFontSubsets+1;
	if ($sip || $smp) {
		$this->fonts[$fontkey] = array('i'=>$i, 'type'=>$type, 'name'=>$name, 'desc'=>$desc, 'panose'=>$panose, 'up'=>$up, 'ut'=>$ut, 'cw'=>$cw, 'ttffile'=>$ttffile, 'fontkey'=>$fontkey, 'subsets'=>array(0=>range(0,127)), 'subsetfontids'=>array($i), 'used'=>false, 'indic'=>$indic, 'sip'=>$sip, 'sipext'=>$sipext, 'smp'=>$smp, 'TTCfontID' => $TTCfontID);
	}
	else  {
		// mPDF 5.3.31
		$ss = array();
		for ($s=32; $s<128; $s++) { $ss[$s] = $s; }
		$this->fonts[$fontkey] = array('i'=>$i, 'type'=>$type, 'name'=>$name, 'desc'=>$desc, 'panose'=>$panose, 'up'=>$up, 'ut'=>$ut, 'cw'=>$cw, 'ttffile'=>$ttffile, 'fontkey'=>$fontkey, 'subset'=>$ss, 'used'=>false, 'indic'=>$indic, 'sip'=>$sip, 'sipext'=>$sipext, 'smp'=>$smp, 'TTCfontID' => $TTCfontID);
	}
	if ($this->useKerning && $haskerninfo) { $this->fonts[$fontkey]['kerninfo'] = $kerninfo; }
	$this->FontFiles[$fontkey]=array('length1'=>$originalsize, 'type'=>"TTF", 'ttffile'=>$ttffile, 'sip'=>$sip, 'smp'=>$smp);
	unset($cw);
}



function SetFont($family,$style='',$size=0, $write=true, $forcewrite=false) {
	$family=strtolower($family);
	// mPDF 5.3.22
	if (!$this->onlyCoreFonts) {
		if ($family == 'sans' || $family == 'sans-serif') { $family = $this->sans_fonts[0]; }
		if ($family == 'serif') { $family = $this->serif_fonts[0]; }
		if ($family == 'mono' || $family == 'monospace') { $family = $this->mono_fonts[0]; }
	}
	// mPDF 5.3.22
	if (isset($this->fonttrans[$family]) && $this->fonttrans[$family]) { $family = $this->fonttrans[$family]; }
	if($family=='') { 
		if ($this->FontFamily) { $family=$this->FontFamily; }
		else if ($this->default_font) { $family=$this->default_font; }
		else { $this->Error("No font or default font set!"); }
	}
	$this->ReqFontStyle = $style;	// required or requested style - used later for artificial bold/italic

	if (($family == 'csymbol') || ($family == 'czapfdingbats')  || ($family == 'ctimes')  || ($family == 'ccourier') || ($family == 'chelvetica')) { 
		if ($this->PDFA || $this->PDFX) {
		   if ($family == 'csymbol' || $family == 'czapfdingbats') { 
			$this->Error("Symbol and Zapfdingbats cannot be embedded in mPDF (required for PDFA1-b or PDFX/1-a).");
		   }
		   if ($family == 'ctimes'  || $family == 'ccourier' || $family == 'chelvetica') { 
			if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) { $this->PDFAXwarnings[] = "Core Adobe font ".ucfirst($family)." cannot be embedded in mPDF, which is required for PDFA1-b or PDFX/1-a. (Embedded font will be substituted.)"; }
			if ($family == 'chelvetica') { $family = 'sans'; }
			if ($family == 'ctimes') { $family = 'serif'; }
			if ($family == 'ccourier') { $family = 'mono'; }
		   }
		   $this->usingCoreFont = false;
		}
		else { $this->usingCoreFont = true; }
		if($family=='csymbol' || $family=='czapfdingbats') { $style=''; }	// mPDF 5.3.05
	}
	else {  $this->usingCoreFont = false; }

	// mPDF 5.3.05
	$this->U=false;
	$this->S=false;
	if ($style) {
		$style=strtoupper($style);
		if(strpos($style,'U')!==false) {
			$this->U=true;
			$style=str_replace('U','',$style);
		}
		if(strpos($style,'S')!==false) {
			$this->S=true;
			// Small Caps
			if (empty($this->upperCase)) { @include(_MPDF_PATH.'includes/upperCase.php'); } 	// mPDF 5.3.99
			$style=str_replace('S','',$style);
		}
		if ($style=='IB') $style='BI';
	}
	if ($size==0) $size=$this->FontSizePt;

	$fontkey=$family.$style;

	$stylekey = $style;
	if (!$stylekey) { $stylekey = "R"; }

	if (!$this->onlyCoreFonts && !$this->usingCoreFont) {
		// mPDF 5.3.05
		if(!isset($this->fonts[$fontkey]) || count($this->default_available_fonts) != count($this->available_unifonts) ) { // not already added
/*-- CJK-FONTS --*/
		  // CJK fonts
		  if (in_array($fontkey,$this->available_CJK_fonts)) {
			if(!isset($this->fonts[$fontkey])) {	// already added
				if (empty($this->Big5_widths)) { require(_MPDF_PATH . 'includes/CJKdata.php'); }
				$this->AddCJKFont($family);	// don't need to add style
			}
		  }
		  // Test to see if requested font/style is available - or substitute
		  else
/*-- END CJK-FONTS --*/
		  if (!in_array($fontkey,$this->available_unifonts)) {
			// If font[nostyle] exists - set it
			if (in_array($family,$this->available_unifonts)) {
				$style = '';
			}

			// Else if only one font available - set it (assumes if only one font available it will not have a style)
			else if (count($this->available_unifonts) == 1) {
				$family = $this->available_unifonts[0];
				$style = '';
			}

			else {
				$found = 0;
				// else substitute font of similar type
				if (in_array($family,$this->sans_fonts)) { 
					$i = array_intersect($this->sans_fonts,$this->available_unifonts);
					if (count($i)) {
						$i = array_values($i);
						// with requested style if possible
						if (!in_array(($i[0].$style),$this->available_unifonts)) {
							$style = '';
						}
						$family = $i[0]; 
						$found = 1;
					}
				}
				else if (in_array($family,$this->serif_fonts)) { 
					$i = array_intersect($this->serif_fonts,$this->available_unifonts);
					if (count($i)) {
						$i = array_values($i);
						// with requested style if possible
						if (!in_array(($i[0].$style),$this->available_unifonts)) {
							$style = '';
						}
						$family = $i[0]; 
						$found = 1;
					}
				}
				else if (in_array($family,$this->mono_fonts)) {
					$i = array_intersect($this->mono_fonts,$this->available_unifonts);
					if (count($i)) {
						$i = array_values($i);
						// with requested style if possible
						if (!in_array(($i[0].$style),$this->available_unifonts)) {
							$style = '';
						}
						$family = $i[0]; 
						$found = 1;
					}
				}

				if (!$found) {
					// set first available font
					$fs = $this->available_unifonts[0];
					preg_match('/^([a-z_0-9\-]+)([BI]{0,2})$/',$fs,$fas);	// Allow "-"
					// with requested style if possible
					$ws = $fas[1].$style;
					if (in_array($ws,$this->available_unifonts)) {
						$family = $fas[1]; // leave $style as is
					}
					else if (in_array($fas[1],$this->available_unifonts)) {
					// or without style
						$family = $fas[1];
						$style = '';
					}
					else {
					// or with the style specified 
						$family = $fas[1];
						$style = $fas[2];
					}
				}
			}
			$fontkey = $family.$style; 
		  }
		}
		// try to add font (if not already added)
		$this->AddFont($family, $style);

		//Test if font is already selected
		if($this->FontFamily == $family && $this->FontFamily == $this->currentfontfamily && $this->FontStyle == $style && $this->FontStyle == $this->currentfontstyle && $this->FontSizePt == $size && $this->FontSizePt == $this->currentfontsize && !$forcewrite) {
			return $family;
		}

		$fontkey = $family.$style; 

		//Select it
		$this->FontFamily = $family;
		$this->FontStyle = $style;
		$this->FontSizePt = $size;
		$this->FontSize = $size / _MPDFK;
		$this->CurrentFont = &$this->fonts[$fontkey];
		if ($write) { 
			$fontout = (sprintf('BT /F%d %.3F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
			if($this->page>0 && ((isset($this->pageoutput[$this->page]['Font']) && $this->pageoutput[$this->page]['Font'] != $fontout) || !isset($this->pageoutput[$this->page]['Font']) || $this->keep_block_together)) { $this->_out($fontout); }
			$this->pageoutput[$this->page]['Font'] = $fontout;
		}



		// Added - currentfont (lowercase) used in HTML2PDF
		$this->currentfontfamily=$family;
		$this->currentfontsize=$size;
		$this->currentfontstyle=$style.($this->U ? 'U' : '').($this->S ? 'S' : '');
		$this->setMBencoding('UTF-8');
	}

	else { 	// if using core fonts


		if ($this->PDFA || $this->PDFX) {
			$this->Error('Core Adobe fonts cannot be embedded in mPDF (required for PDFA1-b or PDFX/1-a) - cannot use option to use core fonts.');
		}
		$this->setMBencoding('windows-1252');

		//Test if font is already selected
		if(($this->FontFamily == $family) AND ($this->FontStyle == $style) AND ($this->FontSizePt == $size) && !$forcewrite) {
			return $family;
		}

		if (!isset($this->CoreFonts[$fontkey])) {
			if (in_array($family,$this->serif_fonts)) { $family = 'ctimes'; }
			else if (in_array($family,$this->mono_fonts)) { $family = 'ccourier'; }
			else { $family = 'chelvetica'; }
			$this->usingCoreFont = true;
			$fontkey = $family.$style; 
		}

		if(!isset($this->fonts[$fontkey])) 	{
			// STANDARD CORE FONTS
			if (isset($this->CoreFonts[$fontkey])) {
				//Load metric file
				$file=$family;
				if($family=='ctimes' || $family=='chelvetica' || $family=='ccourier') { $file.=strtolower($style); }
				$file.='.php';
				include(_MPDF_PATH.'font/'.$file);
				if(!isset($cw)) { $this->Error('Could not include font metric file'); }
				$i=count($this->fonts)+$this->extraFontSubsets+1;
				$this->fonts[$fontkey]=array('i'=>$i,'type'=>'core','name'=>$this->CoreFonts[$fontkey],'desc'=>$desc,'up'=>$up,'ut'=>$ut,'cw'=>$cw);
				if ($this->useKerning) { $this->fonts[$fontkey]['kerninfo'] = $kerninfo; }
			}
			else {
				die('mPDF error - Font not defined');
			}
		}
		//Test if font is already selected
		if(($this->FontFamily == $family) AND ($this->FontStyle == $style) AND ($this->FontSizePt == $size) && !$forcewrite) {
			return $family;
		}
		//Select it
		$this->FontFamily=$family;
		$this->FontStyle=$style;
		$this->FontSizePt=$size;
		$this->FontSize=$size/_MPDFK;
		$this->CurrentFont=&$this->fonts[$fontkey];
		if ($write) { 
			$fontout = (sprintf('BT /F%d %.3F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
			if($this->page>0 && ((isset($this->pageoutput[$this->page]['Font']) && $this->pageoutput[$this->page]['Font'] != $fontout) || !isset($this->pageoutput[$this->page]['Font']) || $this->keep_block_together)) { $this->_out($fontout); }
			$this->pageoutput[$this->page]['Font'] = $fontout;
		}
		// Added - currentfont (lowercase) used in HTML2PDF
		$this->currentfontfamily=$family;
		$this->currentfontsize=$size;
		$this->currentfontstyle=$style.($this->U ? 'U' : '').($this->S ? 'S' : '');

	}

	return $family;
}

function SetFontSize($size,$write=true) {
	//Set font size in points
	if($this->FontSizePt==$size) return;
	$this->FontSizePt=$size;
	$this->FontSize=$size/_MPDFK;
	$this->currentfontsize=$size;
		if ($write) { 
			$fontout = (sprintf('BT /F%d %.3F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
			// Edited mPDF 3.0
			if($this->page>0 && ((isset($this->pageoutput[$this->page]['Font']) && $this->pageoutput[$this->page]['Font'] != $fontout) || !isset($this->pageoutput[$this->page]['Font']) || $this->keep_block_together)) { $this->_out($fontout); }
			$this->pageoutput[$this->page]['Font'] = $fontout;
		}
}

function AddLink() {
	//Create a new internal link
	$n=count($this->links)+1;
	$this->links[$n]=array(0,0);
	return $n;
}

function SetLink($link,$y=0,$page=-1) {
	//Set destination of internal link
	if($y==-1) $y=$this->y;
	if($page==-1)	$page=$this->page;
	$this->links[$link]=array($page,$y);
}

function Link($x,$y,$w,$h,$link) {
	$l = array($x*_MPDFK,$this->hPt-$y*_MPDFK,$w*_MPDFK,$h*_MPDFK,$link);
	if ($this->keep_block_together) {	// Save to array - don't write yet
		$this->ktLinks[$this->page][]= $l;
		return;
	}
	else if ($this->table_rotate) {	// *TABLES*
		$this->tbrot_Links[$this->page][]= $l;	// *TABLES*
		return;	// *TABLES*
	}	// *TABLES*
	else if ($this->kwt) {
		$this->kwt_Links[$this->page][]= $l;
		return;
	}

	if ($this->writingHTMLheader || $this->writingHTMLfooter) {
		$this->HTMLheaderPageLinks[]= $l;
		return;
	}
	//Put a link on the page
	$this->PageLinks[$this->page][]= $l;
	// Save cross-reference to Column buffer
	$ref = count($this->PageLinks[$this->page])-1;	// *COLUMNS*
	$this->columnLinks[$this->CurrCol][INTVAL($this->x)][INTVAL($this->y)] = $ref;	// *COLUMNS*

}

function Text($x,$y,$txt) {
	// Output a string
	// Called (internally) by Watermark and _tableWrite [rotated cells]
	// Expects input to be mb_encoded if necessary and RTL reversed

	// ARTIFICIAL BOLD AND ITALIC
	$s = 'q ';
	if ($this->falseBoldWeight && strpos($this->ReqFontStyle,"B") !== false && strpos($this->FontStyle,"B") === false) {
		$s  .= '2 Tr 1 J 1 j ';
		$s .= sprintf('%.3F w ',($this->FontSize/130)*_MPDFK*$this->falseBoldWeight);
		$tc = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
		if($this->FillColor!=$tc) { $s .= $tc.' '; }		// stroke (outline) = same colour as text(fill)
	}
	if (strpos($this->ReqFontStyle,"I") !== false && strpos($this->FontStyle,"I") === false) {
		$aix = '1 0 0.261799 1 %.3F %.3F Tm'; 
	}
	else { $aix = '%.3F %.3F Td'; }

	if($this->ColorFlag) $s.=$this->TextColor.' ';

	$this->CurrentFont['used']= true;
	if ($this->CurrentFont['type']=='TTF' && ($this->CurrentFont['sip'] || $this->CurrentFont['smp'])) {
	      $txt2 = str_replace(chr(194).chr(160),chr(32),$txt);
		$txt2 = $this->UTF8toSubset($txt2);
		$s.=sprintf('BT '.$aix.' %s Tj ET ',$x*_MPDFK,($this->h-$y)*_MPDFK,$txt2);
	}
	else if (!$this->usingCoreFont) {	// mPDF 5.3.22
	      $txt2 = str_replace(chr(194).chr(160),chr(32),$txt); 
		$this->UTF8StringToArray($txt2);	// this is just to add chars to subset list
		if ($this->kerning && $this->useKerning) { $s .= $this->_kern($txt2, '', $aix, $x, $y); }
		else {
			//Convert string to UTF-16BE without BOM
			$txt2= $this->UTF8ToUTF16BE($txt2, false);
			$s.=sprintf('BT '.$aix.' (%s) Tj ET ',$x*_MPDFK,($this->h-$y)*_MPDFK,$this->_escape($txt2));
		}
	}
	else {
	      $txt2 = str_replace(chr(160),chr(32),$txt);
		if ($this->kerning && $this->useKerning) { $s .= $this->_kern($txt2, '', $aix, $x, $y); }
		else {
			$s.=sprintf('BT '.$aix.' (%s) Tj ET ',$x*_MPDFK,($this->h-$y)*_MPDFK,$this->_escape($txt2));
		}
	}
	if($this->U && $txt!='') {
		// mPDF 5.3.34
		$c = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
		if($this->FillColor!=$c) { $s.= ' '.$c.' '; }
		if (isset($this->CurrentFont['up'])) { $up=$this->CurrentFont['up']; }
		else { $up = -100; }
		$adjusty = (-$up/1000* $this->FontSize);
 		if (isset($this->CurrentFont['ut'])) { $ut=$this->CurrentFont['ut']/1000* $this->FontSize; }
		else { $ut = 60/1000* $this->FontSize; }
		$olw = $this->LineWidth;
		$s.=' '.(sprintf(' %.3F w',$ut*_MPDFK));
		$s.=' '.$this->_dounderline($x,$y + $adjusty,$txt);
		$s.=' '.(sprintf(' %.3F w',$olw*_MPDFK));
		if($this->FillColor!=$c) { $s.= ' '.$this->FillColor.' '; }
	}
   	// STRIKETHROUGH	// mPDF 5.3.54
	if($this->strike && $txt!='') {
		$c = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
		if($this->FillColor!=$c) { $s.= ' '.$c.' '; }
    		//Superscript and Subscript Y coordinate adjustment (now for striked-through texts)
		if (isset($this->CurrentFont['desc']['CapHeight'])) { $ch=$this->CurrentFont['desc']['CapHeight']; }
		else { $ch = 700; }
		$adjusty = (-$ch/1000* $this->FontSize) * 0.35;
 		if (isset($this->CurrentFont['ut'])) { $ut=$this->CurrentFont['ut']/1000* $this->FontSize; }
		else { $ut = 60/1000* $this->FontSize; }
		$olw = $this->LineWidth;
		$s.=' '.(sprintf(' %.3F w',$ut*_MPDFK));
		$s.=' '.$this->_dounderline($x,$y+$adjusty,$txt);
		$s.=' '.(sprintf(' %.3F w',$olw*_MPDFK));
		if($this->FillColor!=$c) { $s.= ' '.$this->FillColor.' '; }
	}
	$s .= 'Q';
	$this->_out($s);
}

/*-- DIRECTW --*/
function WriteText($x,$y,$txt) {
	// Output a string using Text() but does encoding and text reversing of RTL
	$txt = $this->purify_utf8_text($txt);
	if ($this->text_input_as_HTML) {
		$txt = $this->all_entities_to_utf8($txt);
	}
	if ($this->usingCoreFont) { $txt = mb_convert_encoding($txt,$this->mb_enc,'UTF-8'); }	// mPDF 5.3.22
	// DIRECTIONALITY
	if (preg_match("/([".$this->pregRTLchars."])/u", $txt)) { $this->biDirectional = true; }	// *RTL*
	$this->magic_reverse_dir($txt, true, $this->directionality);	// *RTL*
	// Font-specific ligature substitution for Indic fonts
	if (isset($this->CurrentFont['indic']) && $this->CurrentFont['indic']) $this->ConvertIndic($txt);	// *INDIC*
	$this->Text($x,$y,$txt);
}

function WriteCell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='', $currentx=0) {
	//Output a cell using Cell() but does encoding and text reversing of RTL
	$txt = $this->purify_utf8_text($txt);
	if ($this->text_input_as_HTML) {
		$txt = $this->all_entities_to_utf8($txt);
	}
	if ($this->usingCoreFont) { $txt = mb_convert_encoding($txt,$this->mb_enc,'UTF-8'); }	// mPDF 5.3.07	// mPDF 5.3.22
	// DIRECTIONALITY
	if (preg_match("/([".$this->pregRTLchars."])/u", $txt)) { $this->biDirectional = true; }	// *RTL*
	$this->magic_reverse_dir($txt, true, $this->directionality);	// *RTL*
	// Font-specific ligature substitution for Indic fonts
	if (isset($this->CurrentFont['indic']) && $this->CurrentFont['indic']) $this->ConvertIndic($txt);	// *INDIC*
	$this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link, $currentx);
}
/*-- END DIRECTW --*/


function ResetSpacing() {
	if ($this->ws != 0) { $this->_out('BT 0 Tw ET'); }
	$this->ws=0;
	if ($this->charspacing != 0) { $this->_out('BT 0 Tc ET'); }
	$this->charspacing=0;
}


function SetSpacing($cs,$ws) {
	if (intval($cs*1000)==0) { $cs = 0; }
	if ($cs) { $this->_out(sprintf('BT %.3F Tc ET',$cs)); }
	else if ($this->charspacing != 0) { $this->_out('BT 0 Tc ET'); }
	$this->charspacing=$cs;
	if (intval($ws*1000)==0) { $ws = 0; }
	if ($ws) { $this->_out(sprintf('BT %.3F Tw ET',$ws)); }
	else if ($this->ws != 0) { $this->_out('BT 0 Tw ET'); }
	$this->ws=$ws;
}

// WORD SPACING
function GetJspacing($nc,$ns,$w,$inclCursive) {
	$ws = 0; 
	$charspacing = 0;
	$ww = $this->jSWord;
	$ncx = $nc-1;
	if ($nc == 0) { return array(0,0); }
	else if ($nc==1) { $charspacing = $w; }
	// Only word spacing allowed / possible
	else if ($this->fixedlSpacing !== false || $inclCursive) {
		if ($ns) { $ws = $w / $ns; } 
	}
	else if (!$ns) {
		$charspacing = $w / ($ncx );
		if (($this->jSmaxChar > 0) && ($charspacing > $this->jSmaxChar)) { 
			$charspacing = $this->jSmaxChar;
		}
	}
	else if ($ns == ($ncx )) {
		$charspacing = $w / $ns;
	}
	else {
		if ($this->usingCoreFont) {
			$cs = ($w * (1 - $this->jSWord)) / ($ncx );
			if (($this->jSmaxChar > 0) && ($cs > $this->jSmaxChar)) {
				$cs = $this->jSmaxChar;
				$ww = 1 - (($cs * ($ncx ))/$w);
			}
			$charspacing = $cs; 
			$ws = ($w * ($ww) ) / $ns;
		}
		else {
			$cs = ($w * (1 - $this->jSWord)) / ($ncx -$ns);
			if (($this->jSmaxChar > 0) && ($cs > $this->jSmaxChar)) {
				$cs = $this->jSmaxChar;
				$ww = 1 - (($cs * ($ncx -$ns))/$w);
			}
			$charspacing = $cs; 
			$ws = (($w * ($ww) ) / $ns) - $charspacing;
		}
	}
	return array($charspacing,$ws); 
}

function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='', $currentx=0, $lcpaddingL=0, $lcpaddingR=0, $valign='M', $spanfill=0, $abovefont=0, $belowfont=0, $exactWidth=false) {	// mPDF 5.3.07
	//Output a cell
	// Expects input to be mb_encoded if necessary and RTL reversed
	// NON_BREAKING SPACE
	if ($this->usingCoreFont) {
	      $txt = str_replace(chr(160),chr(32),$txt);
	}
	else {
	      $txt = str_replace(chr(194).chr(160),chr(32),$txt);
	}

	$oldcolumn = $this->CurrCol;
	// Automatic page break
	// Allows PAGE-BREAK-AFTER = avoid to work

	if (!$this->tableLevel && (($this->y+$this->divheight>$this->PageBreakTrigger) || ($this->y+$h>$this->PageBreakTrigger) || 
		($this->y+($h*2)>$this->PageBreakTrigger && $this->blk[$this->blklvl]['page_break_after_avoid'])) and !$this->InFooter and $this->AcceptPageBreak()) {
		$x=$this->x;//Current X position


		// WORD SPACING
		$ws=$this->ws;//Word Spacing
		$charspacing=$this->charspacing;//Character Spacing
		$this->ResetSpacing();

		$this->AddPage($this->CurOrientation);
		// Added to correct for OddEven Margins
		$x += $this->MarginCorrection;
		if ($currentx) { 
			$currentx += $this->MarginCorrection;
		} 
		$this->x=$x;
		// WORD SPACING
		$this->SetSpacing($charspacing,$ws);
	}

	// Test: to put line through centre of cell: $this->Line($this->x,$this->y+($h/2),$this->x+50,$this->y+($h/2));

/*-- COLUMNS --*/
	// COLS
	// COLUMN CHANGE
	if ($this->CurrCol != $oldcolumn) {
		if ($currentx) { 
			$currentx += $this->ChangeColumn * ($this->ColWidth+$this->ColGap);
		} 
		$this->x += $this->ChangeColumn * ($this->ColWidth+$this->ColGap);
	}

	// COLUMNS Update/overwrite the lowest bottom of printing y value for a column
	if ($this->ColActive) {
		if ($h) { $this->ColDetails[$this->CurrCol]['bottom_margin'] = $this->y+$h; }
		else { $this->ColDetails[$this->CurrCol]['bottom_margin'] = $this->y+$this->divheight; }
	}
/*-- END COLUMNS --*/

	// KEEP BLOCK TOGETHER Update/overwrite the lowest bottom of printing y value on first page
	if ($this->keep_block_together) {
		if ($h) { $this->ktBlock[$this->page]['bottom_margin'] = $this->y+$h; }
//		else { $this->ktBlock[$this->page]['bottom_margin'] = $this->y+$this->divheight; }
	}

	if($w==0) $w = $this->w-$this->rMargin-$this->x;
	$s='';
	if($fill==1 && $this->FillColor) { 
		if((isset($this->pageoutput[$this->page]['FillColor']) && $this->pageoutput[$this->page]['FillColor'] != $this->FillColor) || !isset($this->pageoutput[$this->page]['FillColor']) || $this->keep_block_together) { $s .= $this->FillColor.' '; }
		$this->pageoutput[$this->page]['FillColor'] = $this->FillColor;
	}


	$boxtop = $this->y;
	$boxheight = $h;
	$boxbottom = $this->y+$h;

	if($txt!='') {
		// FONT SIZE - this determines the baseline caculation
		if ($this->linemaxfontsize && !$this->processingHeader) { $bfs = $this->linemaxfontsize; }
		else  { $bfs = $this->FontSize; }
    		//Calculate baseline Superscript and Subscript Y coordinate adjustment
		$bfx = $this->baselineC;
    		$baseline = $bfx*$bfs;
		if($this->SUP) { $baseline += ($bfx-1.05)*$this->FontSize; }
		else if($this->SUB) { $baseline += ($bfx + 0.04)*$this->FontSize; }
		else if($this->bullet) { $baseline += ($bfx-0.7)*$this->FontSize; }

		// Vertical align (for Images)
		if ($abovefont || $belowfont) {	// from flowing block - valign always M
			$va = $abovefont + (0.5*$bfs);
		}
		else if ($this->lineheight_correction) { 
			if ($valign == 'T') { $va = (0.5 * $bfs * $this->lineheight_correction); }
			else if ($valign == 'B') { $va = $h-(0.5 * $bfs * $this->lineheight_correction); }
			else { $va = 0.5*$h; }	// Middle
		}
		else { 
			if ($valign == 'T') { $va = (0.5 * $bfs * $this->default_lineheight_correction); }
			else if ($valign == 'B') { $va = $h-(0.5 * $bfs * $this->default_lineheight_correction); }
			else { $va = 0.5*$h; }	// Middle
		}

		// ONLY SET THESE IF WANT TO CONFINE BORDER +/- FILL TO FIT FONTSIZE - NOT FULL CELL AS IS ORIGINAL FUNCTION
		// spanfill or spanborder are set in FlowingBlock functions
		// mPDF 5.3.52
		if ($spanfill || !empty($this->spanborddet) || $link!='') { 	// mPDF 5.3.71
			$exth = 0.2;	// Add to fontsize to increase height of background / link / border
			$boxtop = $this->y+$baseline+$va-($this->FontSize*(1+$exth/2)*(0.5+$bfx));
			$boxheight = $this->FontSize * (1+$exth);
			$boxbottom = $boxtop + $boxheight;
		}
	}

	// mPDF 5.3.61
	$bbw = $tbw = $lbw = $rbw = 0;	// Border widths
	if (!empty($this->spanborddet)) { 
		// mPDF 5.3.76
		if (!isset($this->spanborddet['B'])) { $this->spanborddet['B'] = array('s' => 0, 'style' => '', 'w' => 0); }
		if (!isset($this->spanborddet['T'])) { $this->spanborddet['T'] = array('s' => 0, 'style' => '', 'w' => 0); }
		if (!isset($this->spanborddet['L'])) { $this->spanborddet['L'] = array('s' => 0, 'style' => '', 'w' => 0); }
		if (!isset($this->spanborddet['R'])) { $this->spanborddet['R'] = array('s' => 0, 'style' => '', 'w' => 0); }
		// mPDF 5.3.61
		$bbw = $this->spanborddet['B']['w'];
		$tbw = $this->spanborddet['T']['w'];
		$lbw = $this->spanborddet['L']['w'];
		$rbw = $this->spanborddet['R']['w'];
	}
	if($fill==1 || $border==1 || !empty($this->spanborddet)) {	// mPDF 5.3.61
		// mPDF 5.3.61
		if (!empty($this->spanborddet)) { 
			if ($fill==1) {
				$s.=sprintf('%.3F %.3F %.3F %.3F re f ',($this->x-$lbw)*_MPDFK,($this->h-$boxtop+$tbw)*_MPDFK,($w+$lbw+$rbw)*_MPDFK,(-$boxheight-$tbw-$bbw)*_MPDFK);
			}
			$s.= ' q ';
			$dashon = 3;
			$dashoff = 3.5;
			$dot = 2.5;
			if($tbw) {	// mPDF 5.3.76
				$short = 0;
				if ($this->spanborddet['T']['style'] == 'dashed') {
					$s.=sprintf(' 0 j 0 J [%.3F %.3F] 0 d ',$tbw*$dashon*_MPDFK,$tbw*$dashoff*_MPDFK);
				}
				else if ($this->spanborddet['T']['style'] == 'dotted') {
					$s.=sprintf(' 1 j 1 J [%.3F %.3F] %.3F d ',0.001,$tbw*$dot*_MPDFK,-$tbw/2*_MPDFK);
					$short = $tbw/2;
				}
				else {
					$s.=' 0 j 0 J [] 0 d ';
				}
				$c = $this->SetDColor($this->spanborddet['T']['c'],true);
				if ($this->spanborddet['T']['style'] == 'double') {
					$s.=sprintf(' %s %.3F w ',$c,$tbw/3*_MPDFK);
					$xadj = $xadj2 = 0; 
					if ($this->spanborddet['L']['style'] == 'double') { $xadj = $this->spanborddet['L']['w']*2/3; }
					if ($this->spanborddet['R']['style'] == 'double') { $xadj2 = $this->spanborddet['R']['w']*2/3; }
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x-$lbw)*_MPDFK,($this->h-$boxtop+$tbw*5/6)*_MPDFK,($this->x+$w+$rbw-$short)*_MPDFK,($this->h-$boxtop+$tbw*5/6)*_MPDFK);
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x-$lbw+$xadj)*_MPDFK,($this->h-$boxtop+$tbw/6)*_MPDFK,($this->x+$w+$rbw-$short-$xadj2)*_MPDFK,($this->h-$boxtop+$tbw/6)*_MPDFK);
				}
				else {
					$s.=sprintf(' %s %.3F w ',$c,$tbw*_MPDFK);
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x-$lbw)*_MPDFK,($this->h-$boxtop+$tbw/2)*_MPDFK,($this->x+$w+$rbw-$short)*_MPDFK,($this->h-$boxtop+$tbw/2)*_MPDFK);
				}
			}
			if($bbw) {	// mPDF 5.3.76
				$short = 0;
				if ($this->spanborddet['B']['style'] == 'dashed') {
					$s.=sprintf(' 0 j 0 J [%.3F %.3F] 0 d ',$bbw*$dashon*_MPDFK,$bbw*$dashoff*_MPDFK);
				}
				else if ($this->spanborddet['B']['style'] == 'dotted') {
					$s.=sprintf(' 1 j 1 J [%.3F %.3F] %.3F d ',0.001,$bbw*$dot*_MPDFK,-$bbw/2*_MPDFK);
					$short = $bbw/2;
				}
				else {
					$s.=' 0 j 0 J [] 0 d ';
				}
				$c = $this->SetDColor($this->spanborddet['B']['c'],true);
				if ($this->spanborddet['B']['style'] == 'double') {
					$s.=sprintf(' %s %.3F w ',$c,$bbw/3*_MPDFK);
					$xadj = $xadj2 = 0; 
					if ($this->spanborddet['L']['style'] == 'double') { $xadj = $this->spanborddet['L']['w']*2/3; }
					if ($this->spanborddet['R']['style'] == 'double') { $xadj2 = $this->spanborddet['R']['w']*2/3; }
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x-$lbw+$xadj)*_MPDFK,($this->h-$boxbottom-$bbw/6)*_MPDFK,($this->x+$w+$rbw-$short-$xadj2)*_MPDFK,($this->h-$boxbottom-$bbw/6)*_MPDFK);
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x-$lbw)*_MPDFK,($this->h-$boxbottom-$bbw*5/6)*_MPDFK,($this->x+$w+$rbw-$short)*_MPDFK,($this->h-$boxbottom-$bbw*5/6)*_MPDFK);
				}
				else {
					$s.=sprintf(' %s %.3F w ',$c,$bbw*_MPDFK);
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x-$lbw)*_MPDFK,($this->h-$boxbottom-$bbw/2)*_MPDFK,($this->x+$w+$rbw-$short)*_MPDFK,($this->h-$boxbottom-$bbw/2)*_MPDFK);
				}
			}
			if($lbw) {	// mPDF 5.3.76
				$short = 0;
				if ($this->spanborddet['L']['style'] == 'dashed') {
					$s.=sprintf(' 0 j 0 J [%.3F %.3F] 0 d ',$lbw*$dashon*_MPDFK,$lbw*$dashoff*_MPDFK);
				}
				else if ($this->spanborddet['L']['style'] == 'dotted') {
					$s.=sprintf(' 1 j 1 J [%.3F %.3F] %.3F d ',0.001,$lbw*$dot*_MPDFK,-$lbw/2*_MPDFK);
					$short = $lbw/2;
				}
				else {
					$s.=' 0 j 0 J [] 0 d ';
				}
				$c = $this->SetDColor($this->spanborddet['L']['c'],true);
				if ($this->spanborddet['L']['style'] == 'double') {
					$s.=sprintf(' %s %.3F w ',$c,$lbw/3*_MPDFK);
					$yadj = $yadj2 = 0; 
					if ($this->spanborddet['T']['style'] == 'double') { $yadj = $this->spanborddet['T']['w']*2/3; }
					if ($this->spanborddet['B']['style'] == 'double') { $yadj2 = $this->spanborddet['B']['w']*2/3; }
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x-$lbw/6)*_MPDFK,($this->h-$boxtop+$tbw-$yadj)*_MPDFK,($this->x-$lbw/6)*_MPDFK,($this->h-$boxbottom-$bbw+$short+$yadj2)*_MPDFK);
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x-$lbw*5/6)*_MPDFK,($this->h-$boxtop+$tbw)*_MPDFK,($this->x-$lbw*5/6)*_MPDFK,($this->h-$boxbottom-$bbw+$short)*_MPDFK);
				}
				else {
					$s.=sprintf(' %s %.3F w ',$c,$lbw*_MPDFK);
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x-$lbw/2)*_MPDFK,($this->h-$boxtop+$tbw)*_MPDFK,($this->x-$lbw/2)*_MPDFK,($this->h-$boxbottom-$bbw+$short)*_MPDFK);
				}
			}
			if($rbw) {	// mPDF 5.3.76
				$short = 0;
				if ($this->spanborddet['R']['style'] == 'dashed') {
					$s.=sprintf(' 0 j 0 J [%.3F %.3F] 0 d ',$rbw*$dashon*_MPDFK,$rbw*$dashoff*_MPDFK);
				}
				else if ($this->spanborddet['R']['style'] == 'dotted') {
					$s.=sprintf(' 1 j 1 J [%.3F %.3F] %.3F d ',0.001,$rbw*$dot*_MPDFK,-$rbw/2*_MPDFK);
					$short = $rbw/2;
				}
				else {
					$s.=' 0 j 0 J [] 0 d ';
				}
				$c = $this->SetDColor($this->spanborddet['R']['c'],true);
				if ($this->spanborddet['R']['style'] == 'double') {
					$s.=sprintf(' %s %.3F w ',$c,$rbw/3*_MPDFK);
					$yadj = $yadj2 = 0; 
					if ($this->spanborddet['T']['style'] == 'double') { $yadj = $this->spanborddet['T']['w']*2/3; }
					if ($this->spanborddet['B']['style'] == 'double') { $yadj2 = $this->spanborddet['B']['w']*2/3; }
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x+$w+$rbw/6)*_MPDFK,($this->h-$boxtop+$tbw-$yadj)*_MPDFK,($this->x+$w+$rbw/6)*_MPDFK,($this->h-$boxbottom-$bbw+$short+$yadj2)*_MPDFK);
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x+$w+$rbw*5/6)*_MPDFK,($this->h-$boxtop+$tbw)*_MPDFK,($this->x+$w+$rbw*5/6)*_MPDFK,($this->h-$boxbottom-$bbw+$short)*_MPDFK);
				}
				else {
					$s.=sprintf(' %s %.3F w ',$c,$rbw*_MPDFK);
					$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($this->x+$w+$rbw/2)*_MPDFK,($this->h-$boxtop+$tbw)*_MPDFK,($this->x+$w+$rbw/2)*_MPDFK,($this->h-$boxbottom-$bbw+$short)*_MPDFK);
				}
			}
			$s.= ' Q ';
		}
		else {
			if ($fill==1) $op=($border==1) ? 'B' : 'f';
			else $op='S';
			$s.=sprintf('%.3F %.3F %.3F %.3F re %s ',$this->x*_MPDFK,($this->h-$boxtop)*_MPDFK,$w*_MPDFK,-$boxheight*_MPDFK,$op);
		}
	}

	if(is_string($border)) {
		$x=$this->x;
		$y=$this->y;
		if(is_int(strpos($border,'L')))
			$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',$x*_MPDFK,($this->h-$boxtop)*_MPDFK,$x*_MPDFK,($this->h-($boxbottom))*_MPDFK);
		if(is_int(strpos($border,'T')))
			$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',$x*_MPDFK,($this->h-$boxtop)*_MPDFK,($x+$w)*_MPDFK,($this->h-$boxtop)*_MPDFK);
		if(is_int(strpos($border,'R')))
			$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',($x+$w)*_MPDFK,($this->h-$boxtop)*_MPDFK,($x+$w)*_MPDFK,($this->h-($boxbottom))*_MPDFK);
		if(is_int(strpos($border,'B')))
			$s.=sprintf('%.3F %.3F m %.3F %.3F l S ',$x*_MPDFK,($this->h-($boxbottom))*_MPDFK,($x+$w)*_MPDFK,($this->h-($boxbottom))*_MPDFK);
	}

	if($txt!='') {
		if ($exactWidth)	// mPDF 5.3.07
			$stringWidth = $w;  
		else 	// mPDF 5.3.07  // mPDF 5.3.20
			$stringWidth = $this->GetStringWidth($txt) + ( $this->charspacing * mb_strlen( $txt, $this->mb_enc ) / _MPDFK )
				 + ( $this->ws * mb_substr_count( $txt, ' ', $this->mb_enc ) / _MPDFK );

		// Set x OFFSET FOR PRINTING
		if($align=='R') {
			$dx=$w-$this->cMarginR - $stringWidth - $lcpaddingR;
		}
		elseif($align=='C') {
			$dx=(($w - $stringWidth )/2);
		}
		elseif($align=='L' or $align=='J') $dx=$this->cMarginL + $lcpaddingL;
    		else $dx = 0;

		if($this->ColorFlag) $s .='q '.$this->TextColor.' ';

		// OUTLINE
		if($this->outline_on && !$this->S) {
			$s .=' '.sprintf('%.3F w',$this->LineWidth*_MPDFK).' ';
			$s .=" $this->DrawColor ";
			$s .=" 2 Tr ";
    		}
		else if ($this->falseBoldWeight && strpos($this->ReqFontStyle,"B") !== false && strpos($this->FontStyle,"B") === false && !$this->S) {	// can't use together with OUTLINE or Small Caps
			$s .= ' 2 Tr 1 J 1 j ';
			$s .= ' '.sprintf('%.3F w',($this->FontSize/130)*_MPDFK*$this->falseBoldWeight).' ';
			$tc = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
			if($this->FillColor!=$tc) { $s .= ' '.$tc.' '; }		// stroke (outline) = same colour as text(fill)
		}

		if (strpos($this->ReqFontStyle,"I") !== false && strpos($this->FontStyle,"I") === false) {	// Artificial italic
			$aix = '1 0 0.261799 1 %.3F %.3F Tm '; 
		}
		else { $aix = '%.3F %.3F Td '; }

		// THE TEXT
		// mPDF 5.3.A2   Capture in substring $sub
		$sub = '';
		$this->CurrentFont['used']= true;

		// WORD SPACING
		// IF multibyte - Tw has no effect - need to use alternative method - do word spacing using an adjustment before each space
		if ($this->ws && !$this->usingCoreFont && !$this->CurrentFont['sip'] && !$this->CurrentFont['smp'] && !$this->S) {
		  $sub .= ' BT 0 Tw ET ';	 
		  if ($this->kerning && $this->useKerning) { $sub .= $this->_kern($txt, 'MBTw', $aix, ($this->x+$dx), ($this->y+$baseline+$va)); }
		  else {
			$space = " ";
			//Convert string to UTF-16BE without BOM
			$space= $this->UTF8ToUTF16BE($space , false);
			$space=$this->_escape($space ); 
			$sub .=sprintf('BT '.$aix,($this->x+$dx)*_MPDFK,($this->h-($this->y+$baseline+$va))*_MPDFK);
			$t = explode(' ',$txt);
			$sub .=sprintf(' %.3F Tc [',$this->charspacing);
			$numt = count($t);	// mPDF 5.3.07
			for($i=0;$i<$numt;$i++) {	// mPDF 5.3.07
				$tx = $t[$i]; 
				//Convert string to UTF-16BE without BOM
				$tx = $this->UTF8ToUTF16BE($tx , false);
				$tx = $this->_escape($tx); 
				$sub .=sprintf('(%s) ',$tx);
				if (($i+1)<$numt) {	// mPDF 5.3.07
					$adj = -($this->ws)*1000/$this->FontSizePt;
					$sub .=sprintf('%d(%s) ',$adj,$space);
				}
			}
			$sub .='] TJ ';
			$sub .=' ET';
		  }
		}
		else {
		  $txt2= $txt;
		  if ($this->CurrentFont['type']=='TTF' && ($this->CurrentFont['sip'] || $this->CurrentFont['smp'])) {
			if ($this->S) { $sub .= $this->_smallCaps($txt2, 'SIPSMP', $aix, $dx, _MPDFK, $baseline, $va); } 
			else {
				$txt2 = $this->UTF8toSubset($txt2);
				$sub .=sprintf('BT '.$aix.' %s Tj ET',($this->x+$dx)*_MPDFK,($this->h-($this->y+$baseline+$va))*_MPDFK,$txt2);
			}
		  }
		  else {
			if ($this->S) { $sub .= $this->_smallCaps($txt2, '', $aix, $dx, _MPDFK, $baseline, $va); } 
			else if ($this->kerning && $this->useKerning) { $sub .= $this->_kern($txt2, '', $aix, ($this->x+$dx), ($this->y+$baseline+$va)); } 
			else {
				if (!$this->usingCoreFont) {
					$txt2 = $this->UTF8ToUTF16BE($txt2, false);
				}
				$txt2=$this->_escape($txt2); 
				$sub .=sprintf('BT '.$aix.' (%s) Tj ET',($this->x+$dx)*_MPDFK,($this->h-($this->y+$baseline+$va))*_MPDFK,$txt2);
			}
		  }
		}
		// UNDERLINE
		if($this->U) {
			$c = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
			if($this->FillColor!=$c) { $sub .= ' '.$c.' '; }
			if (isset($this->CurrentFont['up'])) { $up=$this->CurrentFont['up']; }
			else { $up = -100; }
			$adjusty = (-$up/1000* $this->FontSize);
 			if (isset($this->CurrentFont['ut'])) { $ut=$this->CurrentFont['ut']/1000* $this->FontSize; }
			else { $ut = 60/1000* $this->FontSize; }
			$olw = $this->LineWidth;
			$sub .=' '.(sprintf(' %.3F w 0 j 0 J ',$ut*_MPDFK));	// mPDF 5.3.61
			$sub .=' '.$this->_dounderline($this->x+$dx,$this->y+$baseline+$va+$adjusty,$txt);
			$sub .=' '.(sprintf(' %.3F w 2 j 2 J ',$olw*_MPDFK));	// mPDF 5.3.61
			if($this->FillColor!=$c) { $sub .= ' '.$this->FillColor.' '; }
		}

   		// STRIKETHROUGH
		if($this->strike) {
			$c = strtoupper($this->TextColor); // change 0 0 0 rg to 0 0 0 RG
			if($this->FillColor!=$c) { $sub .= ' '.$c.' '; }
    			//Superscript and Subscript Y coordinate adjustment (now for striked-through texts)
			if (isset($this->CurrentFont['desc']['CapHeight'])) { $ch=$this->CurrentFont['desc']['CapHeight']; }
			else { $ch = 700; }
			$adjusty = (-$ch/1000* $this->FontSize) * 0.35;
 			if (isset($this->CurrentFont['ut'])) { $ut=$this->CurrentFont['ut']/1000* $this->FontSize; }
			else { $ut = 60/1000* $this->FontSize; }
			$olw = $this->LineWidth;
			$sub .=' '.(sprintf(' %.3F w 0 j 0 J ',$ut*_MPDFK));	// mPDF 5.3.61
			$sub .=' '.$this->_dounderline($this->x+$dx,$this->y+$baseline+$va+$adjusty,$txt);
			$sub .=' '.(sprintf(' %.3F w 2 j 2 J ',$olw*_MPDFK));	// mPDF 5.3.54	// mPDF 5.3.61
			if($this->FillColor!=$c) { $sub .= ' '.$this->FillColor.' '; }
		}

		// mPDF 5.3.A2
		// TEXT SHADOW
		if ($this->textshadow) {		// First to process is last in CSS comma separated shadows
			foreach($this->textshadow AS $ts) {
				$s .= ' q ';
				$s .= $this->SetTColor($ts['col'], true)."\n";
				if ($ts['col']{0}==5 && ord($ts['col']{4})<100) {	// RGBa
					$s .= $this->SetAlpha(ord($ts['col']{4})/100, 'Normal', true, 'F')."\n"; 
				}
				else if ($ts['col']{0}==6 && ord($ts['col']{5})<100) {	// CMYKa
					$s .= $this->SetAlpha(ord($ts['col']{5})/100, 'Normal', true, 'F')."\n"; 
				}
				else if ($ts['col']{0}==1 && $ts['col']{2}==1 && ord($ts['col']{3})<100) {	// Gray
					$s .= $this->SetAlpha(ord($ts['col']{3})/100, 'Normal', true, 'F')."\n"; 
				}
				$s .= sprintf(' q 1 0 0 1 %.4F %.4F cm', $ts['x']*_MPDFK, -$ts['y']*_MPDFK)."\n";
				$s .= $sub;
				$s .= ' Q ';
				$s .= ' Q ';
			}
		}

		$s .= $sub;

		// COLOR
		if($this->ColorFlag) $s .=' Q';

		// LINK
		if($link!='') {
			// mPDF 5.3.16
			// Use same co-ordinates as span FILL above
			$this->Link($this->x,$boxtop,$w,$boxheight,$link);
			// Use this to add some padding/spacing around the textbox for the LINK
			// Use same spacing as span FILL above
	//		$hsp = $this->FontSize/7;
	//		$vsp = $this->FontSize/10;
	//		$this->Link($this->x-$hsp,$boxtop-$vsp,$w+2*$hsp,$boxheight+2*$vsp,$link);
		}
	}
	if($s) $this->_out($s);

	// WORD SPACING
	if ($this->ws && !$this->usingCoreFont) {
		$this->_out(sprintf('BT %.3F Tc ET',$this->charspacing));	 
	}
	$this->lasth=$h;
	if( strpos($txt,"\n") !== false) $ln=1; // cell recognizes \n from <BR> tag
	if($ln>0)
	{
		//Go to next line
		$this->y += $h;
		if($ln==1) {
			//Move to next line
			if ($currentx != 0) { $this->x=$currentx; }
			else { $this->x=$this->lMargin; }
   		}
	}
	else $this->x+=$w;


}


function _kern($txt, $mode, $aix, $x, $y) {
   if ($mode == 'MBTw') {	// Multibyte requiring word spacing
		  $space = ' ';
		  //Convert string to UTF-16BE without BOM
		  $space= $this->UTF8ToUTF16BE($space , false);
		  $space=$this->_escape($space ); 
		  $s = sprintf(' BT '.$aix,$x*_MPDFK,($this->h-$y)*_MPDFK);
		  $t = explode(' ',$txt);
		  for($i=0;$i<count($t);$i++) {
			$tx = $t[$i]; 

			$tj = '(';
			$unicode = $this->UTF8StringToArray($tx);
			for($ti=0;$ti<count($unicode);$ti++) {
				if ($ti > 0 && isset($this->CurrentFont['kerninfo'][$unicode[($ti-1)]][$unicode[$ti]]))  {
							$kern = -$this->CurrentFont['kerninfo'][$unicode[($ti-1)]][$unicode[$ti]];
							$tj .= sprintf(')%d(',$kern);
				}
				$tc = code2utf($unicode[$ti]);
				$tc = $this->UTF8ToUTF16BE($tc, false);
				$tj .= $this->_escape($tc); 
			}
			$tj .= ')';
			$s.=sprintf(' %.3F Tc [%s] TJ',$this->charspacing,$tj);


			if (($i+1)<count($t)) {
				$s.=sprintf(' %.3F Tc (%s) Tj',$this->ws+$this->charspacing,$space);
			}
		  }
		  $s.=' ET ';
   }
   else if (!$this->usingCoreFont) {
	$s = '';
	$tj = '(';
	$unicode = $this->UTF8StringToArray($txt);
	for($i=0;$i<count($unicode);$i++) {
		if ($i > 0 && isset($this->CurrentFont['kerninfo'][$unicode[($i-1)]][$unicode[$i]])) {
					$kern = -$this->CurrentFont['kerninfo'][$unicode[($i-1)]][$unicode[$i]];
					$tj .= sprintf(')%d(',$kern);
		}
		$tx = code2utf($unicode[$i]);
		$tx = $this->UTF8ToUTF16BE($tx, false);
		$tj .= $this->_escape($tx); 
	}
	$tj .= ')';
	$s.=sprintf(' BT '.$aix.' [%s] TJ ET ',$x*_MPDFK,($this->h-$y)*_MPDFK,$tj);
   }
   else {	// CORE Font
	$s = '';
	$tj = '(';
	$l = strlen($txt);
	for($i=0;$i<$l;$i++) {
		if ($i > 0 && isset($this->CurrentFont['kerninfo'][$txt[($i-1)]][$txt[$i]])) {
			$kern = -$this->CurrentFont['kerninfo'][$txt[($i-1)]][$txt[$i]];
			$tj .= sprintf(')%d(',$kern);
		}
		$tj .= $this->_escape($txt[$i]); 
	}
	$tj .= ')';
	$s.=sprintf(' BT '.$aix.' [%s] TJ ET ',$x*_MPDFK,($this->h-$y)*_MPDFK,$tj);
   }

   return $s;
}


function _smallCaps($txt, $mode, $aix, $dx, $k, $baseline, $va) {	// mPDF 5.3.76
	$upp = false;
	$str = array();
	$bits = array();
	if (!$this->usingCoreFont) { 
	   $unicode = $this->UTF8StringToArray($txt);
	   foreach($unicode as $char) {
		if ($this->ws && $char == 32) { 	// space
			if (count($str)) { $bits[] = array($upp, $str, false); }
			$bits[] = array(false, array(32), true); 
			$str = array(); 
			$upp = false;
		}
		else if (isset($this->upperCase[$char])) { 
			if (!$upp) { 
				if (count($str)) { $bits[] = array($upp, $str, false); }
				$str = array(); 
			}
			$str[] = $this->upperCase[$char]; 
			if ((!isset($this->CurrentFont['sip']) || !$this->CurrentFont['sip']) && (!isset($this->CurrentFont['smp']) || !$this->CurrentFont['smp'])) {	
				$this->CurrentFont['subset'][$this->upperCase[$char]] = $this->upperCase[$char];
			}
			$upp = true;
		}
		else { 
			if ($upp) { 
				if (count($str)) { $bits[] = array($upp, $str, false); }
				$str = array(); 
			}
			$str[] = $char;
			$upp = false;
		}
	   }
	}
	else {
	   for($i=0;$i<strlen($txt);$i++) {
		if (isset($this->upperCase[ord($txt[$i])]) && $this->upperCase[ord($txt[$i])] < 256) { 
			if (!$upp) { 
				if (count($str)) { $bits[] = array($upp, $str, false); }
				$str = array(); 
			}
			$str[] = $this->upperCase[ord($txt[$i])]; 
			$upp = true;
		}
		else { 
			if ($upp) { 
				if (count($str)) { $bits[] = array($upp, $str, false); }
				$str = array(); 
			}
			$str[] = ord($txt[$i]);
			$upp = false;
		}
	   }
	}
	if (count($str)) { $bits[] = array($upp, $str, false); }

	$fid = $this->CurrentFont['i'];

	$s=sprintf(' BT '.$aix,($this->x+$dx)*$k,($this->h-($this->y+$baseline+$va))*$k);	// mPDF 5.3.76
	foreach($bits AS $b) {
		if ($b[0]) { $upp = true; }
		else { $upp = false; }

		$size = count ($b[1]);
		$txt = '';
		for ($i = 0; $i < $size; $i++) {
			$txt .= code2utf($b[1][$i]); 
		}
		if ($this->usingCoreFont) { 
			$txt = utf8_decode($txt);
		}
		if ($mode == 'SIPSMP') {
			$txt = $this->UTF8toSubset($txt);
		}
		else { 
			if (!$this->usingCoreFont) {
				$txt = $this->UTF8ToUTF16BE($txt, false);
			}
			$txt=$this->_escape($txt); 
			$txt = '('.$txt.')';
		}
		if ($b[2]) { // space
			$s.=sprintf(' /F%d %.3F Tf %d Tz', $fid, $this->FontSizePt, 100); 
			$s.=sprintf(' %.3F Tc', ($this->charspacing+$this->ws));
			$s.=sprintf(' %s Tj', $txt);
		}
		else if ($upp) { 
			$s.=sprintf(' /F%d %.3F Tf', $fid, $this->FontSizePt*$this->smCapsScale); 
			$s.=sprintf(' %d Tz', $this->smCapsStretch); 
			$s.=sprintf(' %.3F Tc', ($this->charspacing*100/$this->smCapsStretch));
			$s.=sprintf(' %s Tj', $txt);
		}
		else { 
			$s.=sprintf(' /F%d %.3F Tf %d Tz', $fid, $this->FontSizePt, 100); 
			$s.=sprintf(' %.3F Tc', ($this->charspacing)); 
			$s.=sprintf(' %s Tj', $txt);
		}
	}
	$s.=' ET ';
	return $s;
}


function MultiCell($w,$h,$txt,$border=0,$align='',$fill=0,$link='',$directionality='ltr',$encoded=false)
{
	// Parameter (pre-)encoded - When called internally from ToC or textarea: mb_encoding already done - but not reverse RTL/Indic
	if (!$encoded) {
		$txt = $this->purify_utf8_text($txt);
		if ($this->text_input_as_HTML) {
			$txt = $this->all_entities_to_utf8($txt);
		}
		if ($this->usingCoreFont) { $txt = mb_convert_encoding($txt,$this->mb_enc,'UTF-8'); }
		// Font-specific ligature substitution for Indic fonts
		else if (isset($this->CurrentFont['indic']) && $this->CurrentFont['indic']) {	// *INDIC*
			$this->ConvertIndic($tmp);	// *INDIC*
		}	// *INDIC*
		if (preg_match("/([".$this->pregRTLchars."])/u", $txt)) { $this->biDirectional = true; }	// *RTL*
	}
	if (!$align) { $align = $this->defaultAlign; }

	//Output text with automatic or explicit line breaks
	$cw=&$this->CurrentFont['cw'];
	if($w==0)	$w=$this->w-$this->rMargin-$this->x;

	$wmax = ($w - ($this->cMarginL+$this->cMarginR));
	if ($this->usingCoreFont)  {
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		while($nb>0 and $s[$nb-1]=="\n")	$nb--;
	}
	else {
		$s=str_replace("\r",'',$txt);
		$nb=mb_strlen($s, $this->mb_enc );
		while($nb>0 and mb_substr($s,$nb-1,1,$this->mb_enc )=="\n")	$nb--;
	}
	$b=0;
	if($border) {
		if($border==1) {
			$border='LTRB';
			$b='LRT';
			$b2='LR';
		}
		else {
			$b2='';
			if(is_int(strpos($border,'L')))	$b2.='L';
			if(is_int(strpos($border,'R')))	$b2.='R';
			$b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
		}
	}
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$ns=0;
	$nl=1;



   if (!$this->usingCoreFont)  {
	$checkCursive=false;
	if ($this->biDirectional) {  $checkCursive=true; }	// *RTL*
	else if (isset($this->CurrentFont['indic']) && $this->CurrentFont['indic']) {  $checkCursive=true; }	// *INDIC*
	while($i<$nb) {
		//Get next character
		$c = mb_substr($s,$i,1,$this->mb_enc );
		if($c == "\n") {
			//Explicit line break
			// WORD SPACING
			$this->ResetSpacing();
			$tmp = rtrim(mb_substr($s,$j,$i-$j,$this->mb_enc));
			// DIRECTIONALITY
			$this->magic_reverse_dir($tmp, true, $directionality);	// *RTL*

			$this->Cell($w,$h,$tmp,$b,2,$align,$fill,$link);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border and $nl==2) $b=$b2;
			continue;
		}
		if($c == " ") {
			$sep=$i;
			$ls=$l;
			$ns++;
		}

		$l += $this->GetCharWidthNonCore($c);	// mPDF 5.3.04

		if($l>$wmax) {
			//Automatic line break
			if($sep==-1) {	// Only one word
				if($i==$j) $i++;
				// WORD SPACING
				$this->ResetSpacing();
				$tmp = rtrim(mb_substr($s,$j,$i-$j,$this->mb_enc));
				// DIRECTIONALITY
				$this->magic_reverse_dir($tmp, true, $directionality);	// *RTL*

				$this->Cell($w,$h,$tmp,$b,2,$align,$fill,$link);
			}
			else {
				$tmp = rtrim(mb_substr($s,$j,$sep-$j,$this->mb_enc));
				if($align=='J') {
					//////////////////////////////////////////
					// JUSTIFY J using Unicode fonts (Word spacing doesn't work)
					// WORD SPACING UNICODE
					// Change NON_BREAKING SPACE to spaces so they are 'spaced' properly
					$tmp = str_replace(chr(194).chr(160),chr(32),$tmp ); 
					$len_ligne = $this->GetStringWidth($tmp );
					$nb_carac = mb_strlen( $tmp , $this->mb_enc ) ;  
					$nb_spaces = mb_substr_count( $tmp ,' ', $this->mb_enc ) ;  

					$inclCursive=false;
					if ($checkCursive) {
						if (preg_match("/([".$this->pregRTLchars."])/u", $tmp)) { $inclCursive = true; }	// *RTL*
						if (preg_match("/([".$this->pregHIchars.$this->pregBNchars.$this->pregPAchars."])/u", $tmp)) { $inclCursive = true; }	// *INDIC*
					}
					list($charspacing,$ws) = $this->GetJspacing($nb_carac,$nb_spaces,((($wmax) - $len_ligne) * _MPDFK),$inclCursive);
					$this->SetSpacing($charspacing,$ws);
					//////////////////////////////////////////
				}

				// DIRECTIONALITY
				$this->magic_reverse_dir($tmp, true, $directionality);	// *RTL*

				$this->Cell($w,$h,$tmp,$b,2,$align,$fill,$link);
				$i=$sep+1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border and $nl==2) $b=$b2;
		}
		else $i++;
	}
	//Last chunk
	// WORD SPACING

	$this->ResetSpacing();

   }


   else {

	while($i<$nb) {
		//Get next character
		$c=$s[$i];
		if($c == "\n") {
			//Explicit line break
			// WORD SPACING
			$this->ResetSpacing();
			$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill,$link);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border and $nl==2) $b=$b2;
			continue;
		}
		if($c == " ") {
			$sep=$i;
			$ls=$l;
			$ns++;
		}

		$l += $this->GetCharWidthCore($c);	// mPDF 5.3.04
		if($l>$wmax) {
			//Automatic line break
			if($sep==-1) {
				if($i==$j) $i++;
				// WORD SPACING
				$this->ResetSpacing();
				$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill,$link);
			}
			else {
				if($align=='J') {
					$tmp = rtrim(substr($s,$j,$sep-$j));
					//////////////////////////////////////////
					// JUSTIFY J using Unicode fonts (Word spacing doesn't work)
					// WORD SPACING NON_UNICDOE/CJK
					// Change NON_BREAKING SPACE to spaces so they are 'spaced' properly
					$tmp = str_replace(chr(160),chr(32),$tmp);
					$len_ligne = $this->GetStringWidth($tmp );
					$nb_carac = strlen( $tmp ) ;  
					$nb_spaces = substr_count( $tmp ,' ' ) ;  
					list($charspacing,$ws) = $this->GetJspacing($nb_carac,$nb_spaces,((($wmax) - $len_ligne) * _MPDFK),false);
					$this->SetSpacing($charspacing,$ws);
					//////////////////////////////////////////
				}
				$this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill,$link);
				$i=$sep+1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border and $nl==2) $b=$b2;
		}
		else $i++;
	}
	//Last chunk
	// WORD SPACING

	$this->ResetSpacing();

   }
	//Last chunk
   if($border and is_int(strpos($border,'B')))	$b.='B';
   if (!$this->usingCoreFont)  {
		$tmp = rtrim(mb_substr($s,$j,$i-$j,$this->mb_enc));
		// DIRECTIONALITY
		$this->magic_reverse_dir($tmp, true, $directionality);	// *RTL*
   		$this->Cell($w,$h,$tmp,$b,2,$align,$fill,$link);
   }
   else { $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill,$link); }
   $this->x=$this->lMargin;
}


/*-- DIRECTW --*/
function Write($h,$txt,$currentx=0,$link='',$directionality='ltr',$align='') {
	if (!class_exists('directw', false)) { include(_MPDF_PATH.'classes/directw.php'); }
	if (empty($this->directw)) { $this->directw = new directw($this); }
	$this->directw->Write($h,$txt,$currentx,$link,$directionality,$align);
}
/*-- END DIRECTW --*/


/*-- HTML-CSS --*/
function saveInlineProperties() {
	$saved = array();
	$saved[ 'family' ] = $this->FontFamily;
	$saved[ 'style' ] = $this->FontStyle;
	$saved[ 'sizePt' ] = $this->FontSizePt;
	$saved[ 'size' ] = $this->FontSize;
	$saved[ 'HREF' ] = $this->HREF; 
	$saved[ 'underline' ] = $this->U; 
	$saved[ 'smCaps' ] = $this->S;
	$saved[ 'strike' ] = $this->strike;
	$saved[ 'textshadow' ] = $this->textshadow;	// mPDF 5.3.A2
	$saved[ 'SUP' ] = $this->SUP; 
	$saved[ 'SUB' ] = $this->SUB; 
	$saved[ 'linewidth' ] = $this->LineWidth;
	$saved[ 'drawcolor' ] = $this->DrawColor;
	$saved[ 'is_outline' ] = $this->outline_on;
	$saved[ 'outlineparam' ] = $this->outlineparam;
	$saved[ 'toupper' ] = $this->toupper;
	$saved[ 'tolower' ] = $this->tolower;
	$saved[ 'capitalize' ] = $this->capitalize;
	$saved[ 'fontkerning' ] = $this->kerning;
	$saved[ 'lSpacingCSS' ] = $this->lSpacingCSS;
	$saved[ 'wSpacingCSS' ] = $this->wSpacingCSS;
	$saved[ 'I' ] = $this->I;
	$saved[ 'B' ] = $this->B;
	$saved[ 'colorarray' ] = $this->colorarray;
	$saved[ 'bgcolorarray' ] = $this->spanbgcolorarray;
	$saved[ 'border' ] = $this->spanborddet;	// mPDF 5.3.61
	$saved[ 'color' ] = $this->TextColor; 
	$saved[ 'bgcolor' ] = $this->FillColor;
	$saved['lang'] = $this->currentLang;
	$saved['display_off'] = $this->inlineDisplayOff;

	return $saved;
}

function restoreInlineProperties( &$saved) {
	$FontFamily = $saved[ 'family' ];
	$this->FontStyle = $saved[ 'style' ];
	$this->FontSizePt = $saved[ 'sizePt' ];
	$this->FontSize = $saved[ 'size' ];

	$this->currentLang =  $saved['lang'];
	if ($this->useLang && !$this->usingCoreFont) {
	  if ($this->currentLang != $this->default_lang && ((strlen($this->currentLang) == 5 && $this->currentLang != 'UTF-8') || strlen($this->currentLang ) == 2)) { 
		list ($coreSuitable,$mpdf_pdf_unifonts) = GetLangOpts($this->currentLang, $this->useAdobeCJK);
		if ($mpdf_pdf_unifonts) { $this->RestrictUnicodeFonts($mpdf_pdf_unifonts); }
		else { $this->RestrictUnicodeFonts($this->default_available_fonts ); }
	  }
	  else { 
		$this->RestrictUnicodeFonts($this->default_available_fonts );
	  } 
	}

	$this->ColorFlag = ($this->FillColor != $this->TextColor); //Restore ColorFlag as well

	$this->HREF = $saved[ 'HREF' ];
	$this->U = $saved[ 'underline' ];
	$this->S = $saved[ 'smCaps' ];
	$this->strike = $saved[ 'strike' ];
	$this->textshadow = $saved[ 'textshadow' ];	// mPDF 5.3.A2
	$this->SUP = $saved[ 'SUP' ];
	$this->SUB = $saved[ 'SUB' ];
	$this->LineWidth = $saved[ 'linewidth' ];
	$this->DrawColor = $saved[ 'drawcolor' ];
	$this->outline_on = $saved[ 'is_outline' ];
	$this->outlineparam = $saved[ 'outlineparam' ];
	$this->inlineDisplayOff = $saved['display_off'];

	$this->toupper = $saved[ 'toupper' ];
	$this->tolower = $saved[ 'tolower' ];
	$this->capitalize = $saved[ 'capitalize' ];
	$this->kerning = $saved[ 'fontkerning' ];
	$this->lSpacingCSS = $saved[ 'lSpacingCSS' ];
	if (($this->lSpacingCSS || $this->lSpacingCSS==='0') && strtoupper($this->lSpacingCSS) != 'NORMAL') {
		$this->fixedlSpacing = $this->ConvertSize($this->lSpacingCSS,$this->FontSize);
	}
	else { $this->fixedlSpacing = false; }
	$this->wSpacingCSS = $saved[ 'wSpacingCSS' ];
	if ($this->wSpacingCSS && strtoupper($this->wSpacingCSS) != 'NORMAL') { 
		$this->minwSpacing = $this->ConvertSize($this->wSpacingCSS,$this->FontSize);
	}
	else { $this->minwSpacing = 0; }
  
	$this->SetFont($FontFamily, $saved[ 'style' ].($this->U ? 'U' : '').($this->S ? 'S' : ''),$saved[ 'sizePt' ],false);

	$this->currentfontstyle = $saved[ 'style' ].($this->U ? 'U' : '').($this->S ? 'S' : '');
	$this->currentfontsize = $saved[ 'sizePt' ];
	$this->SetStylesArray(array('S'=>$this->S, 'U'=>$this->U, 'B'=>$saved[ 'B' ], 'I'=>$saved[ 'I' ]));

	$this->TextColor = $saved[ 'color' ];
	$this->FillColor = $saved[ 'bgcolor' ];
	$this->colorarray = $saved[ 'colorarray' ];
	$cor = $saved[ 'colorarray' ];
	if ($cor) $this->SetTColor($cor);
	$this->spanbgcolorarray = $saved[ 'bgcolorarray' ];
	$cor = $saved[ 'bgcolorarray' ];
	if ($cor) $this->SetFColor($cor);
	$this->spanborddet = $saved[ 'border' ];	// mPDF 5.3.61
}



// Used when ColActive for tables - updated to return first block with background fill OR borders
function GetFirstBlockFill() {
	// Returns the first blocklevel that uses a bgcolor fill
	$startfill = 0;
	for ($i=1;$i<=$this->blklvl;$i++) {
		if ($this->blk[$i]['bgcolor'] || $this->blk[$i]['border_left']['w'] || $this->blk[$i]['border_right']['w']  || $this->blk[$i]['border_top']['w']  || $this->blk[$i]['border_bottom']['w']  ) {
			$startfill = $i;
			break;
		}
	}
	return $startfill;
}

function SetBlockFill($blvl) {
	if ($this->blk[$blvl]['bgcolor']) {
		$this->SetFColor($this->blk[$blvl]['bgcolorarray']);
		return 1;
	}
	else {
		$this->SetFColor($this->ConvertColor(255));
		return 0;
	}
}


//-------------------------FLOWING BLOCK------------------------------------//
//The following functions were originally written by Damon Kohler           //
//--------------------------------------------------------------------------//

function saveFont() {
	$saved = array();
	$saved[ 'family' ] = $this->FontFamily;
	$saved[ 'style' ] = $this->FontStyle;
	$saved[ 'sizePt' ] = $this->FontSizePt;
	$saved[ 'size' ] = $this->FontSize;
	$saved[ 'curr' ] = &$this->CurrentFont;
	$saved[ 'color' ] = $this->TextColor; 
	$saved[ 'spanbgcolor' ] = $this->spanbgcolor; 
	$saved[ 'spanbgcolorarray' ] = $this->spanbgcolorarray; 
	$saved[ 'bord' ] = $this->spanborder;	// mPDF 5.3.61
	$saved[ 'border' ] = $this->spanborddet;	// mPDF 5.3.61
	$saved[ 'HREF' ] = $this->HREF;
	$saved[ 'underline' ] = $this->U; 
	$saved[ 'smCaps' ] = $this->S;
	$saved[ 'strike' ] = $this->strike;
	$saved[ 'textshadow' ] = $this->textshadow;	// mPDF 5.3.A2
	$saved[ 'SUP' ] = $this->SUP;
	$saved[ 'SUB' ] = $this->SUB;
	$saved[ 'linewidth' ] = $this->LineWidth;
	$saved[ 'drawcolor' ] = $this->DrawColor;
	$saved[ 'is_outline' ] = $this->outline_on;
	$saved[ 'outlineparam' ] = $this->outlineparam;
	$saved[ 'ReqFontStyle' ] = $this->ReqFontStyle;
	$saved[ 'fontkerning' ] = $this->kerning; 
	$saved[ 'fixedlSpacing' ] = $this->fixedlSpacing;
	$saved[ 'minwSpacing' ] = $this->minwSpacing;
	return $saved;
}

function restoreFont( &$saved, $write=true) {
	if (!isset($saved) || empty($saved)) return;

	$this->FontFamily = $saved[ 'family' ];
	$this->FontStyle = $saved[ 'style' ];
	$this->FontSizePt = $saved[ 'sizePt' ];
	$this->FontSize = $saved[ 'size' ];
	$this->CurrentFont = &$saved[ 'curr' ];
	$this->TextColor = $saved[ 'color' ]; 
	$this->spanbgcolor = $saved[ 'spanbgcolor' ]; 
	$this->spanbgcolorarray = $saved[ 'spanbgcolorarray' ]; 
	$this->spanborder = $saved[ 'bord' ];	// mPDF 5.3.61
	$this->spanborddet = $saved[ 'border' ];	// mPDF 5.3.61
	$this->ColorFlag = ($this->FillColor != $this->TextColor); //Restore ColorFlag as well
	$this->HREF = $saved[ 'HREF' ]; 
	$this->U = $saved[ 'underline' ]; 
	$this->S = $saved[ 'smCaps' ];
	$this->kerning = $saved[ 'fontkerning' ];
	$this->fixedlSpacing = $saved[ 'fixedlSpacing' ];
	$this->minwSpacing = $saved[ 'minwSpacing' ];
	$this->strike = $saved[ 'strike' ]; 
	$this->textshadow = $saved[ 'textshadow' ];	// mPDF 5.3.A2
	$this->SUP = $saved[ 'SUP' ]; 
	$this->SUB = $saved[ 'SUB' ]; 
	$this->LineWidth = $saved[ 'linewidth' ]; 
	$this->DrawColor = $saved[ 'drawcolor' ]; 
	$this->outline_on = $saved[ 'is_outline' ]; 
	$this->outlineparam = $saved[ 'outlineparam' ];
	if ($write) { 
		$this->SetFont($saved[ 'family' ],$saved[ 'style' ].($this->U ? 'U' : '').($this->S ? 'S' : ''),$saved[ 'sizePt' ],true,true);	// force output
		$fontout = (sprintf('BT /F%d %.3F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
		if($this->page>0 && ((isset($this->pageoutput[$this->page]['Font']) && $this->pageoutput[$this->page]['Font'] != $fontout) || !isset($this->pageoutput[$this->page]['Font']) || $this->keep_block_together)) { $this->_out($fontout); }
		$this->pageoutput[$this->page]['Font'] = $fontout;
	}
	else 
		$this->SetFont($saved[ 'family' ],$saved[ 'style' ].($this->U ? 'U' : '').($this->S ? 'S' : ''),$saved[ 'sizePt' ]);
	$this->ReqFontStyle = $saved[ 'ReqFontStyle' ];
}

function newFlowingBlock( $w, $h, $a = '', $is_table = false, $is_list = false, $blockstate = 0, $newblock=true, $blockdir='ltr')
{
	if (!$a) { 
		if ($blockdir=='rtl') { $a = 'R'; }
		else { $a = 'L'; }
	}
	$this->flowingBlockAttr[ 'width' ] = ($w * _MPDFK);
	// line height in user units
	$this->flowingBlockAttr[ 'is_table' ] = $is_table;
	$this->flowingBlockAttr[ 'is_list' ] = $is_list;
	$this->flowingBlockAttr[ 'height' ] = $h;
	$this->flowingBlockAttr[ 'lineCount' ] = 0;
	$this->flowingBlockAttr[ 'align' ] = $a;
	$this->flowingBlockAttr[ 'font' ] = array();
	$this->flowingBlockAttr[ 'content' ] = array();
	$this->flowingBlockAttr[ 'contentB' ] = array();	// mPDF 5.3.61
	$this->flowingBlockAttr[ 'contentWidth' ] = 0;
	$this->flowingBlockAttr[ 'blockstate' ] = $blockstate;

	$this->flowingBlockAttr[ 'newblock' ] = $newblock;
	$this->flowingBlockAttr[ 'valign' ] = 'M';
	$this->flowingBlockAttr['blockdir'] = $blockdir;

}

function finishFlowingBlock($endofblock=false, $next='') {
	$currentx = $this->x;
	//prints out the last chunk
	$is_table = $this->flowingBlockAttr[ 'is_table' ];
	$is_list = $this->flowingBlockAttr[ 'is_list' ];
	$maxWidth =& $this->flowingBlockAttr[ 'width' ];
	$lineHeight =& $this->flowingBlockAttr[ 'height' ];
	$align =& $this->flowingBlockAttr[ 'align' ];
	$content =& $this->flowingBlockAttr[ 'content' ];
	$contentB =& $this->flowingBlockAttr[ 'contentB' ];	// mPDF 5.3.61
	$font =& $this->flowingBlockAttr[ 'font' ];
	$contentWidth =& $this->flowingBlockAttr[ 'contentWidth' ];
	$lineCount =& $this->flowingBlockAttr[ 'lineCount' ];
	$valign =& $this->flowingBlockAttr[ 'valign' ];
	$blockstate = $this->flowingBlockAttr[ 'blockstate' ];

	$newblock = $this->flowingBlockAttr[ 'newblock' ];
	$blockdir = $this->flowingBlockAttr['blockdir'];


	// *********** BLOCK BACKGROUND COLOR *****************//
	if ($this->blk[$this->blklvl]['bgcolor'] && !$is_table) {
		$fill = 0;
	}
	else {
		$this->SetFColor($this->ConvertColor(255));
		$fill = 0;
	}

	// Always right trim!
	// Right trim content and adjust width if need to justify (later)
		// mPDF 5.3.76
		if (isset($content[count($content)-1]) && preg_match('/[ ]+$/',$content[count($content)-1], $m)) {
			$strip = strlen($m[0]);
			$content[count($content)-1] = substr($content[count($content)-1],0,(strlen($content[count($content)-1])-$strip));
			$this->restoreFont( $font[ count($content)-1 ],false );
			$contentWidth -= $this->GetStringWidth($m[0]) * _MPDFK;
		}

	// the amount of space taken up so far in user units
	$usedWidth = 0;

	// COLS
	$oldcolumn = $this->CurrCol;

	if ($this->ColActive && !$is_table) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*

	// Print out each chunk

/*-- TABLES --*/
	if ($is_table) { 
		$ipaddingL = 0; 
		$ipaddingR = 0; 
		$paddingL = 0;
		$paddingR = 0;
	} 
	else { 
/*-- END TABLES --*/
		$ipaddingL = $this->blk[$this->blklvl]['padding_left']; 
		$ipaddingR = $this->blk[$this->blklvl]['padding_right']; 
		$paddingL = ($ipaddingL * _MPDFK); 
		$paddingR = ($ipaddingR * _MPDFK);
		$this->cMarginL =  $this->blk[$this->blklvl]['border_left']['w'];
		$this->cMarginR =  $this->blk[$this->blklvl]['border_right']['w'];

		// Added mPDF 3.0 Float DIV
		$fpaddingR = 0;
		$fpaddingL = 0;
/*-- CSS-FLOAT --*/
		if (count($this->floatDivs)) {
			list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
			if ($r_exists) { $fpaddingR = $r_width; }
			if ($l_exists) { $fpaddingL = $l_width; }
		}
/*-- END CSS-FLOAT --*/

		$usey = $this->y + 0.002;
		if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 0) ) { 
			$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
		}
/*-- CSS-IMAGE-FLOAT --*/
		// If float exists at this level
		if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) { $fpaddingR += $this->floatmargins['R']['w']; }
		if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) { $fpaddingL += $this->floatmargins['L']['w']; }
/*-- END CSS-IMAGE-FLOAT --*/
	}	// *TABLES*

		// Set Current lineheight (correction factor)
		$lhfixed = false; 
/*-- LISTS --*/
		if ($is_list) {
			if (preg_match('/([0-9.,]+)mm/',$this->list_lineheight[$this->listlvl][$this->listOcc],$am)) { 
				$lhfixed = true; 
				$def_fontsize = $this->InlineProperties['LISTITEM'][$this->listlvl][$this->listOcc][$this->listnum]['size'];
				$this->lineheight_correction = $am[1] / $def_fontsize ;
			}
			else { 
				$this->lineheight_correction = $this->list_lineheight[$this->listlvl][$this->listOcc]; 
			}
		}
		else
/*-- END LISTS --*/
/*-- TABLES --*/
		if ($is_table) {
			if (preg_match('/([0-9.,]+)mm/',$this->table_lineheight,$am)) { 
				$lhfixed = true; 
				$def_fontsize = $this->FontSize; 				// needs to be default font-size for block ****
				$this->lineheight_correction = $lineHeight / $def_fontsize ; 
			}
			else { 
				$this->lineheight_correction = $this->table_lineheight; 
			}
		}
		else
/*-- END TABLES --*/
		if (isset($this->blk[$this->blklvl]['line_height']) && $this->blk[$this->blklvl]['line_height']) {
			if (preg_match('/([0-9.,]+)mm/',$this->blk[$this->blklvl]['line_height'],$am)) { 
				$lhfixed = true; 
				$def_fontsize = $this->blk[$this->blklvl]['InlineProperties']['size']; 	// needs to be default font-size for block ****
				$this->lineheight_correction = $am[1] / $def_fontsize ;
			}
			else { 
				$this->lineheight_correction = $this->blk[$this->blklvl]['line_height']; 
			}
		} 
		else {
			$this->lineheight_correction = $this->normalLineheight; 
		}

		//  correct lineheight to maximum fontsize
		if ($lhfixed) { $maxlineHeight = $this->lineheight; }
		else { $maxlineHeight = 0; }
		$this->forceExactLineheight = true;
		$maxfontsize = 0;
		// While we're at it, check if contains cursive text
		$checkCursive=false;
		if ($this->biDirectional) {  $checkCursive=true; }	// *RTL*
		foreach ( $content as $k => $chunk )
		{
		  $this->restoreFont( $font[ $k ],false );
		  if (!isset($this->objectbuffer[$k])) { 
			// Soft Hyphens chr(173)
			if (!$this->usingCoreFont) {
			      $content[$k] = $chunk = str_replace("\xc2\xad",'',$chunk ); 
				if (isset($this->CurrentFont['indic']) && $this->CurrentFont['indic']) {  $checkCursive=true; }	// *INDIC*
			}
			else if ($this->FontFamily!='csymbol' && $this->FontFamily!='czapfdingbats') {
			      $content[$k] = $chunk = str_replace(chr(173),'',$chunk );
			}
			// Special case of sub/sup carried over on its own to last line
			if (($this->SUB || $this->SUP) && count($content)==1) { $actfs = $this->FontSize*100/55; } // 55% is font change for sub/sup
			else { $actfs = $this->FontSize; }
			if (!$lhfixed) { $maxlineHeight = max($maxlineHeight,$actfs * $this->lineheight_correction ); }
			if ($lhfixed && ($actfs > $def_fontsize || ($actfs > ($lineHeight * $this->lineheight_correction) && $is_list))) { 
				$this->forceExactLineheight = false; 
			}
			$maxfontsize = max($maxfontsize,$actfs);
		  }
		}

		if(isset($font[count($font)-1])) {
			$lastfontreqstyle = $font[count($font)-1]['ReqFontStyle'];
			$lastfontstyle = $font[count($font)-1]['style'];
		}
		else {
			$lastfontreqstyle=null;
			$lastfontstyle=null;
		}
		if ($blockdir == 'ltr' && strpos($lastfontreqstyle,"I") !== false && strpos($lastfontstyle,"I") === false) {	// Artificial italic
			$lastitalic = $this->FontSize*0.15*_MPDFK;
		}
		else { $lastitalic = 0; }


/*-- LISTS --*/
		if ($is_list && is_array($this->bulletarray) && count($this->bulletarray)) {
	  		$actfs = $this->bulletarray['fontsize'];
			if (!$lhfixed) { $maxlineHeight = max($maxlineHeight,$actfs * $this->lineheight_correction );  }
			if ($lhfixed && $actfs > $def_fontsize) { $this->forceExactLineheight = false; }
			$maxfontsize = max($maxfontsize,$actfs);
		}
/*-- END LISTS --*/

		// when every text item checked i.e. $maxfontsize is set properly

		$af = 0; 	// Above font
		$bf = 0; 	// Below font
		$mta = 0;	// Maximum top-aligned 
		$mba = 0;	// Maximum bottom-aligned 

		foreach ( $content as $k => $chunk )
		{
		  if (isset($this->objectbuffer[$k])) { 
			$oh = $this->objectbuffer[$k]['OUTER-HEIGHT'];
			$va = $this->objectbuffer[$k]['vertical-align']; // = $objattr['vertical-align'] = set as M,T,B,S
			if ($lhfixed && $oh > $def_fontsize) { $this->forceExactLineheight = false; }

			if ($va == 'BS') {	//  (BASELINE default)
				$af = max($af, ($oh - ($maxfontsize * (0.5 + $this->baselineC))));
			}
			else if ($va == 'M') { 
				$af = max($af, ($oh - $maxfontsize)/2);
				$bf = max($bf, ($oh - $maxfontsize)/2);
			}
			else if ($va == 'TT') { 
				$bf = max($bf, ($oh - $maxfontsize));
			}
			else if ($va == 'TB') { 
				$af = max($af, ($oh - $maxfontsize));
			}
			else if ($va == 'T') { 
				$mta = max($mta, $oh);
			}
			else if ($va == 'B') { 
				$mba = max($mba, $oh);
			}
		  }
		}
		if ((!$lhfixed || !$this->forceExactLineheight) && ($af > (($maxlineHeight - $maxfontsize)/2) || $bf > (($maxlineHeight - $maxfontsize)/2))) {
			$maxlineHeight = $maxfontsize + $af + $bf;
		}
		else if (!$lhfixed) { $af = $bf = ($maxlineHeight - $maxfontsize)/2; }
		if ($mta > $maxlineHeight) { 
			$bf += ($mta - $maxlineHeight);
			$maxlineHeight = $mta;
		}
		if ($mba > $maxlineHeight) { 
			$af += ($mba - $maxlineHeight);
			$maxlineHeight = $mba;
		}

		$lineHeight = $maxlineHeight;
		// If NOT images, and maxfontsize NOT > lineHeight - this value determines text baseline positioning
		if ($lhfixed && $af==0 && $bf==0 && $maxfontsize<=($def_fontsize * $this->lineheight_correction * 0.8 )) { 
			$this->linemaxfontsize = $def_fontsize; 
		}
		else { $this->linemaxfontsize = $maxfontsize; }

		// Get PAGEBREAK TO TEST for height including the bottom border/padding
		$check_h = max($this->divheight,$lineHeight);

		// This fixes a proven bug...
		if ($endofblock && $newblock && $blockstate==0 && !$content) {  $check_h = 0; }
		// but ? needs to fix potentially more widespread...
	//	if (!$content) {  $check_h = 0; }

		if ($this->blklvl > 0 && !$is_table) { 
		   if ($endofblock && $blockstate > 1) { 
			if ($this->blk[$this->blklvl]['page_break_after_avoid']) {  $check_h += $lineHeight; }
			$check_h += ($this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w']);
		   }
		   if (($newblock && ($blockstate==1 || $blockstate==3) && $lineCount == 0) || ($endofblock && $blockstate > 1 && $lineCount == 0)) { 
			$check_h += ($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['border_top']['w']);
		   }
		}

		// Force PAGE break if column height cannot take check-height
		if ($this->ColActive && $check_h > ($this->PageBreakTrigger - $this->y0)) { 
			$this->SetCol($this->NbCol-1);
		}
 

		// PAGEBREAK
		//'If' below used in order to fix "first-line of other page with justify on" bug
		if(!$is_table && ($this->y+$check_h) > $this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak()) {
    	     		$bak_x=$this->x;//Current X position
			// WORD SPACING
			$ws=$this->ws;//Word Spacing
			$charspacing=$this->charspacing;//Character Spacing
			$this->ResetSpacing();

		      $this->AddPage($this->CurOrientation);

		      $this->x=$bak_x;
			// Added to correct for OddEven Margins
			$currentx += $this->MarginCorrection;
			$this->x += $this->MarginCorrection;

			// WORD SPACING
			$this->SetSpacing($charspacing,$ws);
		}

		if ($this->keep_block_together && !$is_table && $this->kt_p00 < $this->page && ($this->y+$check_h) > $this->kt_y00) {
			$this->printdivbuffer();
			$this->keep_block_together = 0;
		}

/*-- COLUMNS --*/
		// COLS
		// COLUMN CHANGE
		if ($this->CurrCol != $oldcolumn) {
			$currentx += $this->ChangeColumn * ($this->ColWidth+$this->ColGap);
			$this->x += $this->ChangeColumn * ($this->ColWidth+$this->ColGap);
			$oldcolumn = $this->CurrCol;
		}


		if ($this->ColActive && !$is_table) { $this->breakpoints[$this->CurrCol][] = $this->y; }
/*-- END COLUMNS --*/

		// TOP MARGIN
		if ($newblock && ($blockstate==1 || $blockstate==3) && ($this->blk[$this->blklvl]['margin_top']) && $lineCount == 0 && !$is_table && !$is_list) { 
			$this->DivLn($this->blk[$this->blklvl]['margin_top'],$this->blklvl-1,true,$this->blk[$this->blklvl]['margin_collapse']); 
			if ($this->ColActive) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*
		}

		if ($newblock && ($blockstate==1 || $blockstate==3) && $lineCount == 0 && !$is_table && !$is_list) { 
			$this->blk[$this->blklvl]['y0'] = $this->y;
			$this->blk[$this->blklvl]['startpage'] = $this->page;
			if ($this->ColActive) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*
		}

	// ADDED for Paragraph_indent
	$WidthCorrection = 0;
	if (($newblock) && ($blockstate==1 || $blockstate==3) && isset($this->blk[$this->blklvl]['text_indent']) && ($lineCount == 0) && (!$is_table) && (!$is_list) && ($align != 'C')) { 
		$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
		$WidthCorrection = ($ti*_MPDFK); 
	} 


	// PADDING and BORDER spacing/fill
	if (($newblock) && ($blockstate==1 || $blockstate==3) && (($this->blk[$this->blklvl]['padding_top']) || ($this->blk[$this->blklvl]['border_top'])) && ($lineCount == 0) && (!$is_table) && (!$is_list)) { 
			// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
			$this->DivLn($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'],-3,true,false,1); 
			if ($this->ColActive) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*
			$this->x = $currentx;
	}


	// Added mPDF 3.0 Float DIV
	$fpaddingR = 0;
	$fpaddingL = 0;
/*-- CSS-FLOAT --*/
	if (count($this->floatDivs)) {
		list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
		if ($r_exists) { $fpaddingR = $r_width; }
		if ($l_exists) { $fpaddingL = $l_width; }
	}
/*-- END CSS-FLOAT --*/

	$usey = $this->y + 0.002;
	if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 0) ) { 
		$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
	}
/*-- CSS-IMAGE-FLOAT --*/
	// If float exists at this level
	if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) { $fpaddingR += $this->floatmargins['R']['w']; }
	if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) { $fpaddingL += $this->floatmargins['L']['w']; }
/*-- END CSS-IMAGE-FLOAT --*/

	if ($content) {

		// In FinishFlowing Block no lines are justified as it is always last line
		// but if orphansAllowed have allowed content width to go over max width, use J charspacing to compress line
		// JUSTIFICATION J - NOT!
		$nb_carac = 0;
		$nb_spaces = 0;
		$jcharspacing = 0;
		$jws = 0;
		$inclCursive=false;
		foreach ( $content as $k => $chunk ) {
			if (!isset($this->objectbuffer[$k]) || (isset($this->objectbuffer[$k]) && !$this->objectbuffer[$k])) {
				if ($this->usingCoreFont) {
				      $chunk = str_replace(chr(160),chr(32),$chunk );
				}
				else {
				      $chunk = str_replace(chr(194).chr(160),chr(32),$chunk ); 
				}
				$nb_carac += mb_strlen( $chunk, $this->mb_enc );  
				$nb_spaces += mb_substr_count( $chunk,' ', $this->mb_enc );  
				if ($checkCursive) {
					if (preg_match("/([".$this->pregRTLchars."])/u", $chunk)) { $inclCursive = true; }	// *RTL*
					if (preg_match("/([".$this->pregHIchars.$this->pregBNchars.$this->pregPAchars."])/u", $chunk)) { $inclCursive = true; }	// *INDIC*
				}
			}
		}
		// if it's justified, we need to find the char/word spacing (or if orphans have allowed length of line to go over the maxwidth)
		// If "orphans" in fact is just a final space - ignore this
		if (((($contentWidth + $lastitalic) > $maxWidth) && ($content[count($content)-1] != ' ') )  ||
			(!$endofblock && $align=='J' && ($next=='image' || $next=='select' || $next=='input' || $next=='textarea' || ($next=='br' && $this->justifyB4br)))
			) {
 		  // WORD SPACING
			list($jcharspacing,$jws) = $this->GetJspacing($nb_carac,$nb_spaces,($maxWidth-$lastitalic-$contentWidth-$WidthCorrection-(($this->cMarginL+$this->cMarginR)*_MPDFK)-($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * _MPDFK) )),$inclCursive);
		}
		// Check if will fit at word/char spacing of previous line - if so continue it
		// but only allow a maximum of $this->jSmaxWordLast and $this->jSmaxCharLast
		else if ($contentWidth < ($maxWidth - $lastitalic-$WidthCorrection - (($this->cMarginL+$this->cMarginR)* _MPDFK) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * _MPDFK))) && !$this->fixedlSpacing) {
			if ($this->ws > $this->jSmaxWordLast) {
				$jws = $this->jSmaxWordLast;
			}
			if ($this->charspacing > $this->jSmaxCharLast) {
				$jcharspacing = $this->jSmaxCharLast;
			}
			$check = $maxWidth - $lastitalic-$WidthCorrection - $contentWidth - (($this->cMarginL+$this->cMarginR)* _MPDFK) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * _MPDFK) ) - ( $jcharspacing * $nb_carac) - ( $jws * $nb_spaces);
			if ($check <= 0) {
				$jcharspacing = 0;
				$jws = 0;
			}
		}

		$empty = $maxWidth - $lastitalic-$WidthCorrection - $contentWidth - (($this->cMarginL+$this->cMarginR)* _MPDFK) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * _MPDFK) );

		$empty -= ($jcharspacing * $nb_carac);
		$empty -= ($jws * $nb_spaces);

		$empty /= _MPDFK;

		if (!$is_table) { 
			$this->maxPosR = max($this->maxPosR , ($this->w - $this->rMargin - $this->blk[$this->blklvl]['outer_right_margin'] - $empty)); 
			$this->maxPosL = min($this->maxPosL , ($this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'] + $empty)); 
		}

		$arraysize = count($content);

		$margins = ($this->cMarginL+$this->cMarginR) + ($ipaddingL+$ipaddingR + $fpaddingR + $fpaddingR );

		if (!$is_table) { $this->DivLn($lineHeight,$this->blklvl,false); }	// false -> don't advance y

		// DIRECTIONALITY RTL
		$all_rtl = false;
		$contains_rtl = false;
/*-- RTL --*/
   		if ($blockdir == 'rtl' || $this->biDirectional)  {
			$all_rtl = true;
			foreach ( $content as $k => $chunk ) {
				$reversed = $this->magic_reverse_dir($chunk, false, $blockdir);
				if ($reversed > 0) { $contains_rtl = true; }
				if ($reversed < 2) { $all_rtl = false; }
				$content[$k] = $chunk;
			}
			if (($blockdir =='rtl' && $contains_rtl) || $all_rtl) { 
				$content = array_reverse($content,false); 
				$contentB = array_reverse($contentB,false); 	// mPDF 5.3.61
			}
		}
/*-- END RTL --*/

		$this->x = $currentx + $this->cMarginL + $ipaddingL + $fpaddingL;
		if ($align == 'R') { $this->x += $empty; }
		else if ($align == 'J' && $blockdir == 'rtl') { $this->x += $empty; }
		else if ($align == 'C') { $this->x += ($empty / 2); }


		// Paragraph INDENT
		$WidthCorrection = 0; 
		if (($newblock) && ($blockstate==1 || $blockstate==3) && isset($this->blk[$this->blklvl]['text_indent']) && ($lineCount == 0) && (!$is_table) && (!$is_list) && ($align !='C')) { 
			$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
			$this->x += $ti; 
		}


          foreach ( $content as $k => $chunk )
          {

		// FOR IMAGES
		if ((($blockdir == 'rtl') && ($contains_rtl )) || $all_rtl ) { $dirk = $arraysize-1 - $k; } else { $dirk = $k; }

		$va = 'M';	// default for text
		if (isset($this->objectbuffer[$dirk]) && $this->objectbuffer[$dirk]) {
			$xadj = $this->x - $this->objectbuffer[$dirk]['OUTER-X']; 
			$this->objectbuffer[$dirk]['OUTER-X'] += $xadj;
			$this->objectbuffer[$dirk]['BORDER-X'] += $xadj;
			$this->objectbuffer[$dirk]['INNER-X'] += $xadj;
			$va = $this->objectbuffer[$dirk]['vertical-align'];
			$yadj = $this->y - $this->objectbuffer[$dirk]['OUTER-Y'];
			if ($va == 'BS') { 
				$yadj += $af + ($this->linemaxfontsize * (0.5 + $this->baselineC)) - $this->objectbuffer[$dirk]['OUTER-HEIGHT'];
			}
			else if ($va == 'M' || $va == '') { 
				$yadj += $af + ($this->linemaxfontsize /2) - ($this->objectbuffer[$dirk]['OUTER-HEIGHT']/2);
			}
			else if ($va == 'TB') { 
				$yadj += $af + $this->linemaxfontsize - $this->objectbuffer[$dirk]['OUTER-HEIGHT'];
			}
			else if ($va == 'TT') { 
				$yadj += $af;
			}
			else if ($va == 'B') { 
				$yadj += $af + $this->linemaxfontsize + $bf - $this->objectbuffer[$dirk]['OUTER-HEIGHT'];
			}
			else if ($va == 'T') { 
				$yadj += 0;
			}
			$this->objectbuffer[$dirk]['OUTER-Y'] += $yadj;
			$this->objectbuffer[$dirk]['BORDER-Y'] += $yadj;
			$this->objectbuffer[$dirk]['INNER-Y'] += $yadj;
		}



			// DIRECTIONALITY RTL
			if ((($blockdir == 'rtl') && ($contains_rtl )) || $all_rtl ) { $this->restoreFont( $font[ $arraysize-1 - $k ] ); }
			else { $this->restoreFont( $font[ $k ] ); }

			$this->SetSpacing(($this->fixedlSpacing*_MPDFK)+$jcharspacing,($this->fixedlSpacing+$this->minwSpacing)*_MPDFK+$jws);
			$this->fixedlSpacing = false;
			$this->minwSpacing = 0;

	 		// *********** SPAN BACKGROUND COLOR ***************** //
			if (isset($this->spanbgcolor) && $this->spanbgcolor) { 
				$cor = $this->spanbgcolorarray;
				$this->SetFColor($cor);
				$save_fill = $fill; $spanfill = 1; $fill = 1;
			}
			// mPDF 5.3.61
			if (!empty($this->spanborddet)) { 
				if (strpos($contentB[$k],'L')!==false && isset($this->spanborddet['L'])) $this->x += $this->spanborddet['L']['w'];
				if (strpos($contentB[$k],'L')===false) $this->spanborddet['L']['s'] = $this->spanborddet['L']['w'] = 0; 
				if (strpos($contentB[$k],'R')===false) $this->spanborddet['R']['s'] = $this->spanborddet['R']['w'] = 0; 
			}
			// WORD SPACING
		      $stringWidth = $this->GetStringWidth($chunk ) + ( $this->charspacing * mb_strlen($chunk,$this->mb_enc ) / _MPDFK )  
				+ ( $this->ws * mb_substr_count($chunk,' ',$this->mb_enc ) / _MPDFK );
			if (isset($this->objectbuffer[$dirk])) { 
				if ($this->objectbuffer[$dirk]['type']=='dottab') { 
					$this->objectbuffer[$dirk]['OUTER-WIDTH'] +=$empty; 
				}
				$stringWidth = $this->objectbuffer[$dirk]['OUTER-WIDTH'];
			}

			if ($stringWidth==0) { $stringWidth = 0.000001; }
			if ($k == $arraysize-1) $this->Cell( $stringWidth, $lineHeight, $chunk, '', 1, '', $fill, $this->HREF , $currentx,0,0,'M', $fill, $af, $bf, true ); //mono-style line or last part (skips line)	// mPDF 5.3.07
			else $this->Cell( $stringWidth, $lineHeight, $chunk, '', 0, '', $fill, $this->HREF, 0, 0,0,'M', $fill, $af, $bf, true );//first or middle part	// mPDF 5.3.07

			// mPDF 5.3.61
			if (!empty($this->spanborddet)) { 
				if (strpos($contentB[$k],'R')!==false && $k != $arraysize-1)  $this->x += $this->spanborddet['R']['w'];
			}
	 		// *********** SPAN BACKGROUND COLOR OFF - RESET BLOCK BGCOLOR ***************** //
			if (isset($spanfill) && $spanfill) { 
				$fill = $save_fill; $spanfill = 0; 
				if ($fill) { $this->SetFColor($bcor); }
			}
          }

	$this->printobjectbuffer($is_table, $blockdir);


	$this->objectbuffer = array();


	$this->ResetSpacing();

/*-- LISTS --*/
	// LIST BULLETS/NUMBERS
	if ($is_list && is_array($this->bulletarray) && ($lineCount == 0) ) {
	  
	  $savedFont = $this->saveFont();

	  $bull = $this->bulletarray;
	  if (isset($bull['level']) && isset($bull['occur']) && isset($this->InlineProperties['LIST'][$bull['level']][$bull['occur']])) { 
		$this->restoreInlineProperties($this->InlineProperties['LIST'][$bull['level']][$bull['occur']]); 
	  }
	  if (isset($bull['level']) && isset($bull['occur']) && isset($bull['num']) && isset($this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]) && $this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]) { $this->restoreInlineProperties($this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]); }
	  if (isset($bull['font']) && $bull['font'] == 'czapfdingbats') {
		$this->bullet = true;
		$this->SetFont('czapfdingbats','',$this->FontSizePt/2.5);
	  }
	  else { $this->SetFont($this->FontFamily,$this->FontStyle,$this->FontSizePt,true,true); }	// force output
       //Output bullet
	  $this->x = $currentx;
	  if (isset($bull['x'])) { $this->x += $bull['x']; }
	  $this->y -= $lineHeight;
	  if (is_array($bull['col'])) { $this->SetTColor($bull['col']); }

        if (isset($bull['txt'])) { $this->Cell($bull['w'], $lineHeight,$bull['txt'],'','',$bull['align'],0,'',0,-$this->cMarginL, -$this->cMarginR ); }
	  if (isset($bull['font']) && $bull['font'] == 'czapfdingbats') {
		$this->bullet = false;
	  }
	  $this->x = $currentx;	// Reset
	  $this->y += $lineHeight;

	  if ($this->ColActive && !$is_table) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*

	   $this->restoreFont( $savedFont );
	  // $font = array( $savedFont );

	  $this->bulletarray = array();	// prevents repeat of bullet/number if <li>....<br />.....</li>
	}
/*-- END LISTS --*/


	}	// END IF CONTENT

/*-- CSS-IMAGE-FLOAT --*/
	// Update values if set to skipline
	if ($this->floatmargins) { $this->_advanceFloatMargins(); }


	if ($endofblock && $blockstate>1) { 
		// If float exists at this level
		if (isset($this->floatmargins['R']['y1'])) { $fry1 = $this->floatmargins['R']['y1']; }
		else { $fry1 = 0; }
		if (isset($this->floatmargins['L']['y1'])) { $fly1 = $this->floatmargins['L']['y1']; }
		else { $fly1 = 0; }
		if ($this->y < $fry1 || $this->y < $fly1) { 
			$drop = max($fry1,$fly1) - $this->y;
			$this->DivLn($drop); 
			$this->x = $currentx;
		}
	}
/*-- END CSS-IMAGE-FLOAT --*/


	// PADDING and BORDER spacing/fill
	if ($endofblock && ($blockstate > 1) && ($this->blk[$this->blklvl]['padding_bottom'] || $this->blk[$this->blklvl]['border_bottom'] || $this->blk[$this->blklvl]['css_set_height']) && (!$is_table) && (!$is_list)) { 
			// If CSS height set, extend bottom - if on same page as block started, and CSS HEIGHT > actual height, 
			//	and does not force pagebreak
			$extra = 0;
			if ($this->blk[$this->blklvl]['css_set_height'] && $this->blk[$this->blklvl]['startpage']==$this->page) {
				// predicted height
				$h1 = ($this->y-$this->blk[$this->blklvl]['y0']) + $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w'];
				if ($h1 < ($this->blk[$this->blklvl]['css_set_height']+$this->blk[$this->blklvl]['padding_bottom']+$this->blk[$this->blklvl]['padding_top'])) { $extra = ($this->blk[$this->blklvl]['css_set_height']+$this->blk[$this->blklvl]['padding_bottom']+$this->blk[$this->blklvl]['padding_top']) - $h1; }
				if($this->y + $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w'] + $extra > $this->PageBreakTrigger) {
					$extra = $this->PageBreakTrigger - ($this->y + $this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w']); 
				}
			}

			// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
			$this->DivLn($this->blk[$this->blklvl]['padding_bottom'] + $this->blk[$this->blklvl]['border_bottom']['w'] + $extra,-3,true,false,2); 
			$this->x = $currentx;

			if ($this->ColActive) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*

	}

	// SET Bottom y1 of block (used for painting borders)
	if (($endofblock) && ($blockstate > 1) && (!$is_table) && (!$is_list)) { 
		$this->blk[$this->blklvl]['y1'] = $this->y;
	}

	// BOTTOM MARGIN
	if (($endofblock) && ($blockstate > 1) && ($this->blk[$this->blklvl]['margin_bottom']) && (!$is_table) && (!$is_list)) { 
		if($this->y+$this->blk[$this->blklvl]['margin_bottom'] < $this->PageBreakTrigger and !$this->InFooter) {
		  $this->DivLn($this->blk[$this->blklvl]['margin_bottom'],$this->blklvl-1,true,$this->blk[$this->blklvl]['margin_collapse']); 
		  if ($this->ColActive) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*
		}
	}

	// Reset lineheight
	$lineHeight = $this->divheight;
}





function printobjectbuffer($is_table=false, $blockdir=false) {
		if (!$blockdir) { $blockdir = $this->directionality; }
		if ($is_table && $this->shrin_k > 1) { $k = $this->shrin_k; } 
		else { $k = 1; }
		$save_y = $this->y;
		$save_x = $this->x;
		$save_currentfontfamily = $this->FontFamily;
		$save_currentfontsize = $this->FontSizePt;
		$save_currentfontstyle = $this->FontStyle.($this->U ? 'U' : '').($this->S ? 'S' : '');
		if ($blockdir == 'rtl') { $rtlalign = 'R'; } else { $rtlalign = 'L'; }
		foreach ($this->objectbuffer AS $ib => $objattr) { 
		   if ($objattr['type'] == 'bookmark' || $objattr['type'] == 'indexentry' || $objattr['type'] == 'toc') {
			$x = $objattr['OUTER-X']; 
			$y = $objattr['OUTER-Y'];
			$this->y = $y - $this->FontSize/2;
			$this->x = $x;
			if ($objattr['type'] == 'bookmark' ) { $this->Bookmark($objattr['CONTENT'],$objattr['bklevel'] ,$y - $this->FontSize); }	// *BOOKMARKS*
			if ($objattr['type'] == 'indexentry') { $this->IndexEntry($objattr['CONTENT']); }	// *INDEX*
			if ($objattr['type'] == 'toc') { $this->TOC_Entry($objattr['CONTENT'], $objattr['toclevel'], $objattr['toc_id']); }	// *TOC*
		   }
/*-- ANNOTATIONS --*/
		   else if ($objattr['type'] == 'annot') {
			if ($objattr['POS-X']) { $x = $objattr['POS-X']; }
			else if ($this->annotMargin<>0) { $x = -$objattr['OUTER-X']; }
			else { $x = $objattr['OUTER-X']; }
			if ($objattr['POS-Y']) { $y = $objattr['POS-Y']; }
			else { $y = $objattr['OUTER-Y'] - $this->FontSize/2; }
			// Create a dummy entry in the _out/columnBuffer with position sensitive data,
			// linking $y-1 in the Columnbuffer with entry in $this->columnAnnots
			// and when columns are split in length will not break annotation from current line
			$this->y = $y-1;
			$this->x = $x-1;
			$this->Line($x-1,$y-1,$x-1,$y-1);
			$this->Annotation($objattr['CONTENT'], $x , $y , $objattr['ICON'], $objattr['AUTHOR'], $objattr['SUBJECT'], $objattr['OPACITY'], $objattr['COLOR'], $objattr['POPUP'], $objattr['FILE']);
		   }
/*-- END ANNOTATIONS --*/
		   else { 
			$y = $objattr['OUTER-Y'];
			$x = $objattr['OUTER-X'];
			$w = $objattr['OUTER-WIDTH'];
			$h = $objattr['OUTER-HEIGHT'];
			if (isset($objattr['text'])) { $texto = $objattr['text']; }
			$this->y = $y;
			$this->x = $x;
			if (isset($objattr['fontfamily'])) { $this->SetFont($objattr['fontfamily'],'',$objattr['fontsize'] ); }
		   }

		// HR
		   if ($objattr['type'] == 'hr') {
			$this->SetDColor($objattr['color']);
			switch($objattr['align']) {
      		    case 'C':
      		        $empty = $objattr['OUTER-WIDTH'] - $objattr['INNER-WIDTH'];
      		        $empty /= 2;
      		        $x += $empty;
     		        	  break;
      		    case 'R':
      		        $empty = $objattr['OUTER-WIDTH'] - $objattr['INNER-WIDTH'];
      		        $x += $empty;
      		        break;
			}
      		$oldlinewidth = $this->LineWidth;
			$this->SetLineWidth($objattr['linewidth']/$k );
			$this->y += ($objattr['linewidth']/2) + $objattr['margin_top']/$k;
			$this->Line($x,$this->y,$x+$objattr['INNER-WIDTH'],$this->y);
			$this->SetLineWidth($oldlinewidth);
			$this->SetDColor($this->ConvertColor(0));
		   }
		// IMAGE
		   if ($objattr['type'] == 'image') {
			// mPDF 5.3.42	VISIBILITY // mPDF 5.3.49
			if(isset($objattr['visibility']) && $objattr['visibility']!='visible' && $objattr['visibility']) {
				$this->SetVisibility($objattr['visibility']);
			}
			if (isset($objattr['opacity'])) { $this->SetAlpha($objattr['opacity']); }
			$rotate = 0;
			$obiw = $objattr['INNER-WIDTH'];
			$obih = $objattr['INNER-HEIGHT'];
			$sx = $objattr['INNER-WIDTH']*_MPDFK / $objattr['orig_w'];
			$sy = abs($objattr['INNER-HEIGHT'])*_MPDFK / abs($objattr['orig_h']);
			$sx = ($objattr['INNER-WIDTH']*_MPDFK / $objattr['orig_w']);
			$sy = ($objattr['INNER-HEIGHT']*_MPDFK / $objattr['orig_h']);

			if (isset($objattr['ROTATE'])) { $rotate = $objattr['ROTATE']; }
			if ($rotate==90) { 
				// Clockwise
				$obiw = $objattr['INNER-HEIGHT'];
				$obih = $objattr['INNER-WIDTH'];
				$tr = $this->transformTranslate(0, -$objattr['INNER-WIDTH'], true) ;
				$tr .= ' '. $this->transformRotate(90, $objattr['INNER-X'],($objattr['INNER-Y'] +$objattr['INNER-WIDTH'] ),true) ;
				$sx = $obiw*_MPDFK / $objattr['orig_h'];
				$sy = $obih*_MPDFK / $objattr['orig_w'];
			}
			else if ($rotate==-90 || $rotate==270) { 
				// AntiClockwise
				$obiw = $objattr['INNER-HEIGHT'];
				$obih = $objattr['INNER-WIDTH'];
				$tr = $this->transformTranslate($objattr['INNER-WIDTH'], ($objattr['INNER-HEIGHT']-$objattr['INNER-WIDTH']), true) ;
				$tr .= ' '. $this->transformRotate(-90, $objattr['INNER-X'],($objattr['INNER-Y'] +$objattr['INNER-WIDTH'] ),true) ;
				$sx = $obiw*_MPDFK / $objattr['orig_h'];
				$sy = $obih*_MPDFK / $objattr['orig_w'];
			}
			else if ($rotate==180) { 
				// Mirror
				$tr = $this->transformTranslate($objattr['INNER-WIDTH'], -$objattr['INNER-HEIGHT'], true) ;
				$tr .= ' '. $this->transformRotate(180, $objattr['INNER-X'],($objattr['INNER-Y'] +$objattr['INNER-HEIGHT'] ),true) ;
			}
			else { $tr = ''; }
			$tr = trim($tr);
			if ($tr) { $tr .= ' '; }
			$gradmask = '';


/*-- BACKGROUNDS --*/
			if (isset($objattr['GRADIENT-MASK'])) { 
				$g = $this->grad->parseMozGradient( $objattr['GRADIENT-MASK'] );
				if ($g) {
					$dummy = $this->grad->Gradient($objattr['INNER-X'], $objattr['INNER-Y'], $obiw, $obih, $g['type'], $g['stops'], $g['colorspace'], $g['coords'], $g['extend'], true, true);
					$gradmask = '/TGS'.count($this->gradients).' gs ';
					// $this->_out("q ".$tr.$this->grad->Gradient($objattr['INNER-X'], $objattr['INNER-Y'], $obiw, $obih, $g['type'], $g['stops'], $g['colorspace'], $g['coords'], $g['extend'], true)." Q");
				}
			}
/*-- END BACKGROUNDS --*/
/*-- IMAGES-WMF --*/
			if (isset($objattr['itype']) && $objattr['itype']=='wmf') { 
				$outstring = sprintf('q '.$tr.'%.3F 0 0 %.3F %.3F %.3F cm /FO%d Do Q', $sx, -$sy, $objattr['INNER-X']*_MPDFK-$sx*$objattr['wmf_x'], (($this->h-$objattr['INNER-Y'])*_MPDFK)+$sy*$objattr['wmf_y'], $objattr['ID']);
			}
			else  
/*-- END IMAGES-WMF --*/
			if (isset($objattr['itype']) && $objattr['itype']=='svg') { 
				$outstring = sprintf('q '.$tr.'%.3F 0 0 %.3F %.3F %.3F cm /FO%d Do Q', $sx, -$sy, $objattr['INNER-X']*_MPDFK-$sx*$objattr['wmf_x'], (($this->h-$objattr['INNER-Y'])*_MPDFK)+$sy*$objattr['wmf_y'], $objattr['ID']);
			}
			else { 
				$outstring = sprintf("q ".$tr."%.3F 0 0 %.3F %.3F %.3F cm ".$gradmask."/I%d Do Q",$obiw*_MPDFK, $obih*_MPDFK, $objattr['INNER-X'] *_MPDFK, ($this->h-($objattr['INNER-Y'] +$obih ))*_MPDFK,$objattr['ID'] );
			}
			$this->_out($outstring);
			// LINK
			if (isset($objattr['link'])) $this->Link($objattr['INNER-X'],$objattr['INNER-Y'],$objattr['INNER-WIDTH'],$objattr['INNER-HEIGHT'],$objattr['link']);
			if (isset($objattr['opacity'])) { $this->SetAlpha(1); }
			if ((isset($objattr['border_top']) && $objattr['border_top']>0) || (isset($objattr['border_left']) && $objattr['border_left']>0) || (isset($objattr['border_right']) && $objattr['border_right']>0) || (isset($objattr['border_bottom']) && $objattr['border_bottom']>0)) { $this->PaintImgBorder($objattr,$is_table); }
			// mPDF 5.3.42	VISIBILITY // mPDF 5.3.49
			if(isset($objattr['visibility']) && $objattr['visibility']!='visible' && $objattr['visibility']) {
				$this->SetVisibility('visible');
			}

		   }

/*-- BARCODES --*/
		// BARCODE
		   if ($objattr['type'] == 'barcode') {
			$bgcol = $this->ConvertColor(255);
			if (isset($objattr['bgcolor']) && $objattr['bgcolor']) {	// mPDF 5.3.A4
				$bgcol = $objattr['bgcolor'];
			}
			$col = $this->ConvertColor(0);
			if (isset($objattr['color']) && $objattr['color']) {	// mPDF 5.3.A4
				$col = $objattr['color'];
			}
			$this->SetFColor($bgcol);
	 		$this->Rect($objattr['BORDER-X'], $objattr['BORDER-Y'], $objattr['BORDER-WIDTH'], $objattr['BORDER-HEIGHT'], 'F');
			$this->SetFColor($this->ConvertColor(255));
			if (isset($objattr['BORDER-WIDTH'])) { $this->PaintImgBorder($objattr,$is_table); }
			if ($objattr['btype'] == 'EAN13' || $objattr['btype'] == 'ISBN' || $objattr['btype'] == 'ISSN' || $objattr['btype'] == 'UPCA' || $objattr['btype'] == 'UPCE' || $objattr['btype'] == 'EAN8') {
				$this->WriteBarcode($objattr['code'], $objattr['showtext'], $objattr['INNER-X'], $objattr['INNER-Y'], $objattr['bsize'], 0, 0, 0, 0, 0, $objattr['bheight'], $bgcol, $col, $objattr['btype'], $objattr['bsupp'], $objattr['bsupp_code'], $k);
			}
			// QR-code
			else if ($objattr['btype']=='QR') {
				if (!class_exists('QRcode', false)) { 
					include(_MPDF_PATH.'qrcode/qrcode.class.php'); 
				}
				$this->qrcode = new QRcode($objattr['code'], $objattr['errorlevel']);
				$this->qrcode->displayFPDF($this, $objattr['INNER-X'], $objattr['INNER-Y'], $objattr['bsize']*25, array(255,255,255), array(0,0,0));
			}
			else {
				$this->WriteBarcode2($objattr['code'], $objattr['INNER-X'], $objattr['INNER-Y'], $objattr['bsize'], $objattr['bheight'], $bgcol, $col, $objattr['btype'], $objattr['pr_ratio'], $k);
			}
		   }
/*-- END BARCODES --*/

		// mPDF 5.3.A5
		// TEXT CIRCLE
		   if ($objattr['type'] == 'textcircle') {
			$bgcol = $this->ConvertColor(255);
			if (isset($objattr['bgcolor']) && $objattr['bgcolor']) {
				$bgcol = $objattr['bgcolor'];
			}
			$col = $this->ConvertColor(0);
			if (isset($objattr['color']) && $objattr['color']) {
				$col = $objattr['color'];
			}
			$this->SetTColor($col);
			$this->SetFColor($bgcol);
	 		$this->Rect($objattr['BORDER-X'], $objattr['BORDER-Y'], $objattr['BORDER-WIDTH'], $objattr['BORDER-HEIGHT'], 'F');
			$this->SetFColor($this->ConvertColor(255));
			if (isset($objattr['BORDER-WIDTH'])) { $this->PaintImgBorder($objattr,$is_table); }
			if (!class_exists('directw', false)) { include(_MPDF_PATH.'classes/directw.php'); }
			if (empty($this->directw)) { $this->directw = new directw($this); }
			$save_lmfs = $this->linemaxfontsize;
			$this->linemaxfontsize = 0;
			if (isset($objattr['top-text'])) {
				$this->directw->CircularText($objattr['INNER-X']+$objattr['INNER-WIDTH']/2, $objattr['INNER-Y']+$objattr['INNER-HEIGHT']/2, $objattr['r']/$k, $objattr['top-text'], 'top', $objattr['fontfamily'], $objattr['fontsize']/$k, $objattr['fontstyle'], $objattr['space-width'], $objattr['char-width']);
			}
			if (isset($objattr['bottom-text'])) {
				$this->directw->CircularText($objattr['INNER-X']+$objattr['INNER-WIDTH']/2, $objattr['INNER-Y']+$objattr['INNER-HEIGHT']/2, $objattr['r']/$k, $objattr['bottom-text'], 'bottom', $objattr['fontfamily'], $objattr['fontsize']/$k, $objattr['fontstyle'], $objattr['space-width'], $objattr['char-width']);
			}
			$this->linemaxfontsize = $save_lmfs;
		   }

		   $this->ResetSpacing();

		// DOT-TAB
		   if ($objattr['type'] == 'dottab') {
				$sp = $this->GetStringWidth(' ');
				$nb=floor(($w-2*$sp)/$this->GetStringWidth('.'));
				if ($nb>0) { $dots=' '.str_repeat('.',$nb).' '; }
				else { $dots=' '; }
				$col = $this->ConvertColor(0);
				if (isset($objattr['colorarray']) && is_array($objattr['colorarray'])) {
					$col = $objattr['colorarray'];
				}
				$this->SetTColor($col);
				// mPDF 5.3.A3
				$save_sbd = $this->spanborddet;
				$save_u = $this->U;
				$save_s = $this->strike;
				$this->spanborddet = '';
				$this->U = false;
				$this->strike = false;
				$this->Cell($w,$h,$dots,0,0,'C');
				$this->spanborddet = $save_sbd;
				$this->U = $save_u;
				$this->strike = $save_s;
				// mPDF 5.0
				$this->SetTColor($this->ConvertColor(0));
		   }

/*-- FORMS --*/
		// TEXT/PASSWORD INPUT
		   if ($objattr['type'] == 'input' && ($objattr['subtype'] == 'TEXT' || $objattr['subtype'] == 'PASSWORD')) {
			$this->form->print_ob_text($objattr,$w,$h,$texto,$rtlalign,$k,$blockdir);
		   }

		// TEXTAREA
		   if ($objattr['type'] == 'textarea') {
			$this->form->print_ob_textarea($objattr,$w,$h,$texto,$rtlalign,$k,$blockdir);
		   }

		// SELECT
		   if ($objattr['type'] == 'select') {
			$this->form->print_ob_select($objattr,$w,$h,$texto,$rtlalign,$k,$blockdir);
		   }


		// INPUT/BUTTON as IMAGE
		   if ($objattr['type'] == 'input' && $objattr['subtype'] == 'IMAGE') {
			$this->form->print_ob_imageinput($objattr,$w,$h,$texto,$rtlalign,$k,$blockdir);
		   }

		// BUTTON
		   if ($objattr['type'] == 'input' && ($objattr['subtype'] == 'SUBMIT' || $objattr['subtype'] == 'RESET' || $objattr['subtype'] == 'BUTTON')) {
			$this->form->print_ob_button($objattr,$w,$h,$texto,$rtlalign,$k,$blockdir);
		   }

		// CHECKBOX
		   if ($objattr['type'] == 'input' && ($objattr['subtype'] == 'CHECKBOX')) {
			$this->form->print_ob_checkbox($objattr,$w,$h,$texto,$rtlalign,$k,$blockdir,$x,$y);
		   }
		// RADIO
		   if ($objattr['type'] == 'input' && ($objattr['subtype'] == 'RADIO')) {
			$this->form->print_ob_radio($objattr,$w,$h,$texto,$rtlalign,$k,$blockdir,$x,$y);
		   }
/*-- END FORMS --*/
		}
		$this->SetFont($save_currentfontfamily,$save_currentfontstyle,$save_currentfontsize);
		$this->y = $save_y;
		$this->x = $save_x;
		unset($content);
}


function WriteFlowingBlock( $s)
{
	$currentx = $this->x; 
	$is_table = $this->flowingBlockAttr[ 'is_table' ];
	$is_list = $this->flowingBlockAttr[ 'is_list' ];
	// width of all the content so far in points
	$contentWidth =& $this->flowingBlockAttr[ 'contentWidth' ];
	// cell width in points
	$maxWidth =& $this->flowingBlockAttr[ 'width' ];
	$lineCount =& $this->flowingBlockAttr[ 'lineCount' ];
	// line height in user units
	$lineHeight =& $this->flowingBlockAttr[ 'height' ];
	$align =& $this->flowingBlockAttr[ 'align' ];
	$content =& $this->flowingBlockAttr[ 'content' ];
	$contentB =& $this->flowingBlockAttr[ 'contentB' ];	// mPDF 5.3.61
	$font =& $this->flowingBlockAttr[ 'font' ];
	$valign =& $this->flowingBlockAttr[ 'valign' ];
	$blockstate = $this->flowingBlockAttr[ 'blockstate' ];

	$newblock = $this->flowingBlockAttr[ 'newblock' ];
	$blockdir = $this->flowingBlockAttr['blockdir'];
	// *********** BLOCK BACKGROUND COLOR ***************** //
	if ($this->blk[$this->blklvl]['bgcolor'] && !$is_table) {
		$fill = 0;
	}
	else {
		$this->SetFColor($this->ConvertColor(255));
		$fill = 0;
	}
	$font[] = $this->saveFont();
	$content[] = '';
	$contentB[] = '';	// mPDF 5.3.61
	$currContent =& $content[ count( $content ) - 1 ];
	// where the line should be cutoff if it is to be justified
	$cutoffWidth = $contentWidth;

	$curlyquote = mb_convert_encoding("\xe2\x80\x9e",$this->mb_enc,'UTF-8');
	$curlylowquote = mb_convert_encoding("\xe2\x80\x9d",$this->mb_enc,'UTF-8');

	$CJKoverflow = false;

	// COLS
	$oldcolumn = $this->CurrCol;

	if ($this->ColActive && !$is_table) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*

/*-- TABLES --*/
   if ($is_table) { 
	$ipaddingL = 0; 
	$ipaddingR = 0; 
	$paddingL = 0; 
	$paddingR = 0; 
	$cpaddingadjustL = 0;
	$cpaddingadjustR = 0;
 	// Added mPDF 3.0
	$fpaddingR = 0;
	$fpaddingL = 0;
  } 
   else { 
/*-- END TABLES --*/
		$ipaddingL = $this->blk[$this->blklvl]['padding_left']; 
		$ipaddingR = $this->blk[$this->blklvl]['padding_right']; 
		$paddingL = ($ipaddingL * _MPDFK); 
		$paddingR = ($ipaddingR * _MPDFK); 
		$this->cMarginL =  $this->blk[$this->blklvl]['border_left']['w'];
		$cpaddingadjustL = -$this->cMarginL;
		$this->cMarginR =  $this->blk[$this->blklvl]['border_right']['w'];
		$cpaddingadjustR = -$this->cMarginR;
		// Added mPDF 3.0 Float DIV
		$fpaddingR = 0;
		$fpaddingL = 0;
/*-- CSS-FLOAT --*/
		if (count($this->floatDivs)) {
			list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
			if ($r_exists) { $fpaddingR = $r_width; }
			if ($l_exists) { $fpaddingL = $l_width; }
		}
/*-- END CSS-FLOAT --*/

		$usey = $this->y + 0.002;
		if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 0) ) { 
			$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
		}
/*-- CSS-IMAGE-FLOAT --*/
		// If float exists at this level
		if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) { $fpaddingR += $this->floatmargins['R']['w']; }
		if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) { $fpaddingL += $this->floatmargins['L']['w']; }
/*-- END CSS-IMAGE-FLOAT --*/
   }	// *TABLES*

     //OBJECTS - IMAGES & FORM Elements (NB has already skipped line/page if required - in printbuffer)
      if (substr($s,0,3) == "\xbb\xa4\xac") { //identifier has been identified!
		$objattr = $this->_getObjAttr($s);
		$h_corr = 0; 
		if ($is_table) {	// *TABLES*
			$maximumW = ($maxWidth/_MPDFK) - ($this->cellPaddingL + $this->cMarginL + $this->cellPaddingR + $this->cMarginR); 	// *TABLES*
		}	// *TABLES*
		else {	// *TABLES*
			if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 0) && (!$is_table)) { $h_corr = $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w']; }
			$maximumW = ($maxWidth/_MPDFK) - ($this->blk[$this->blklvl]['padding_left'] + $this->blk[$this->blklvl]['border_left']['w'] + $this->blk[$this->blklvl]['padding_right'] + $this->blk[$this->blklvl]['border_right']['w'] + $fpaddingL + $fpaddingR ); 
		}	// *TABLES*
		$objattr = $this->inlineObject($objattr['type'],$this->lMargin + $fpaddingL + ($contentWidth/_MPDFK),($this->y + $h_corr), $objattr, $this->lMargin,($contentWidth/_MPDFK),$maximumW,$lineHeight,true,$is_table);

		// SET LINEHEIGHT for this line ================ RESET AT END
		$lineHeight = MAX($lineHeight,$objattr['OUTER-HEIGHT']);
		$this->objectbuffer[count($content)-1] = $objattr;
		// if (isset($objattr['vertical-align'])) { $valign = $objattr['vertical-align']; }
		// else { $valign = ''; }
		$contentWidth += ($objattr['OUTER-WIDTH'] * _MPDFK);
		return;
	}

	// mPDF 5.3.61
	$lbw = $rbw = 0;	// Border widths
	if (!empty($this->spanborddet)) { 
		if (isset($this->spanborddet['L'])) $lbw = $this->spanborddet['L']['w'];
		if (isset($this->spanborddet['R'])) $rbw = $this->spanborddet['R']['w'];
	}

   if ($this->usingCoreFont) {
	$tmp = strlen( $s );
   }
   else {
	$tmp = mb_strlen( $s, $this->mb_enc );
   }

   $orphs = 0; 
   $check = 0;

   // for every character in the string
   for ( $i = 0; $i < $tmp; $i++ )  {
	// extract the current character
	// get the width of the character in points
	if ($this->usingCoreFont) {
       	$c = $s[$i];
		// Soft Hyphens chr(173)
		// mPDF 5.3.07
		$cw = ($this->GetCharWidthCore($c) * _MPDFK);	// mPDF 5.3.04
		if ($this->kerning && $this->useKerning && $i > 0) {
			if (isset($this->CurrentFont['kerninfo'][$s[($i-1)]][$c])) { 
				$cw += ($this->CurrentFont['kerninfo'][$s[($i-1)]][$c] * $this->FontSizePt / 1000 );
			}
		}
	}
	else {
	      $c = mb_substr($s,$i,1,$this->mb_enc );
		$cw = ($this->GetCharWidthNonCore($c, false) * _MPDFK);	// mPDF 5.3.04
		if ($this->kerning && $this->useKerning && $i > 0) {
	     		$lastc = mb_substr($s,($i-1),1,$this->mb_enc );
			$ulastc = $this->UTF8StringToArray($lastc, false);
			$uc = $this->UTF8StringToArray($c, false);
			if (isset($this->CurrentFont['kerninfo'][$ulastc[0]][$uc[0]])) { 
				$cw += ($this->CurrentFont['kerninfo'][$ulastc[0]][$uc[0]] * $this->FontSizePt / 1000 );
			}
		}
	}

	// mPDF 5.3.61
	if ($i==0) {
		$cw += $lbw*_MPDFK;
		$contentB[(count($contentB)-1)] .= 'L';
	}
	if ($i==($tmp-1)) {
		$cw += $rbw*_MPDFK;
		$contentB[(count($contentB)-1)] .= 'R';
	}

	if ($c==' ') { $check = 1; }

	// CHECK for ORPHANS
	else if ($c=='.' || $c==',' || $c==')' || $c==']' || $c==';' || $c==':' || $c=='!' || $c=='?'|| $c=='"' || $c==$curlyquote || $c==$curlylowquote)  {$check++; }
/*-- CJK-FONTS --*/
	else if ($this->checkCJK) { // mPDF 5.3.07
		if ((!$is_table && $this->CJKfollowing && preg_match("/[".$this->CJKfollowing."]/u", $c)) || ($is_table && preg_match("/[".$this->CJKoverflow ."]/u", $c)))  { $check++; }
		else { $check = 0; }
	}
/*-- END CJK-FONTS --*/
	else { $check = 0; }
 
	// There's an orphan '. ' or ', ' or <sup>32</sup> about to be cut off at the end of line
	if($check==1) {
		$currContent .= $c;
		$cutoffWidth = $contentWidth;
		$contentWidth += $cw;
		continue;
	}
	if(($this->SUP || $this->SUB) && ($orphs < $this->orphansAllowed)) {	// ? disable orphans in table if  borders used
		$currContent .= $c;
		$cutoffWidth = $contentWidth;
		$contentWidth += $cw;
		$orphs++;
		continue;
	}
	else { $orphs = 0; }

	// ADDED for Paragraph_indent
	$WidthCorrection = 0; 
	if (($newblock) && ($blockstate==1 || $blockstate==3) && isset($this->blk[$this->blklvl]['text_indent']) && ($lineCount == 0) && (!$is_table) && (!$is_list) && ($align != 'C')) { 
		$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
		$WidthCorrection = ($ti*_MPDFK); 
	} 

	// Added mPDF 3.0 Float DIV
	$fpaddingR = 0;
	$fpaddingL = 0;
/*-- CSS-FLOAT --*/
	if (count($this->floatDivs)) {
		list($l_exists, $r_exists, $l_max, $r_max, $l_width, $r_width) = $this->GetFloatDivInfo($this->blklvl);
		if ($r_exists) { $fpaddingR = $r_width; }
		if ($l_exists) { $fpaddingL = $l_width; }
	}
/*-- END CSS-FLOAT --*/

	$usey = $this->y + 0.002;
	if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 0) ) { 
		$usey += $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'];
	}

/*-- CSS-IMAGE-FLOAT --*/
	// If float exists at this level
	if (isset($this->floatmargins['R']) && $usey <= $this->floatmargins['R']['y1'] && $usey >= $this->floatmargins['R']['y0'] && !$this->floatmargins['R']['skipline']) { $fpaddingR += $this->floatmargins['R']['w']; }
	if (isset($this->floatmargins['L']) && $usey <= $this->floatmargins['L']['y1'] && $usey >= $this->floatmargins['L']['y0'] && !$this->floatmargins['L']['skipline']) { $fpaddingL += $this->floatmargins['L']['w']; }
/*-- END CSS-IMAGE-FLOAT --*/



       // try adding another char
	if (( $contentWidth + $cw > $maxWidth - $WidthCorrection - (($this->cMarginL+$this->cMarginR)*_MPDFK) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * _MPDFK) ) +  0.001))  {// 0.001 is to correct for deviations converting mm=>pts
		// it won't fit, output what we already have
		$lineCount++;

		// contains any content that didn't make it into this print
		$savedContent = '';
		$savedContentB = '';	// mPDF 5.3.61
		$savedFont = array();
		$savedObj = array();

		// cut off and save any partial words at the end of the string
		$words = explode( ' ', $currContent ); 
		///////////////////
		// HYPHENATION
		$currWord = $words[count($words)-1] ;
		$success = false;
/*-- HYPHENATION --*/
		// Soft Hyphens chr(173)
		if ((!$this->usingCoreFont && preg_match("/\xc2\xad/",$currWord)) || ($this->usingCoreFont && preg_match("/".chr(173)."/",$currWord) && ($this->FontFamily!='csymbol' && $this->FontFamily!='czapfdingbats')) ) {
			$rem = $maxWidth - $WidthCorrection - (($this->cMarginL+$this->cMarginR)*_MPDFK) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * _MPDFK) );
			list($success,$pre,$post,$prelength) = $this->softHyphenate($currWord, (($rem-$cutoffWidth)/_MPDFK -$this->GetCharWidthNonCore(" ", false)) );	// mPDF 5.3.04
		}

		if (!$success && ($this->hyphenate || ($this->hyphenateTables && $is_table))) { 
			// Look ahead to get current word
			for($ac = $i; $ac<(mb_strlen($s)-1); $ac++) {
				$addc = mb_substr($s,$ac,1,$this->mb_enc );
				if ($addc == ' ') { break; }
				$currWord .= $addc;
			}
			$rem = $maxWidth - $WidthCorrection - (($this->cMarginL+$this->cMarginR)*_MPDFK) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * _MPDFK) );
			list($success,$pre,$post,$prelength) = $this->hyphenateWord($currWord, (($rem-$cutoffWidth)/_MPDFK -$this->GetCharWidth(" ", false)) );
		}
		if ($success) { 
			$already = array_pop( $words );
			$forward = mb_substr($already,$prelength,mb_strlen($already, $this->mb_enc), $this->mb_enc);
			$words[] = $pre.'-';
			$words[] = $forward;
			$currContent = mb_substr($currContent,0,mb_strlen($currContent, $this->mb_enc)+1-mb_strlen($post, $this->mb_enc), $this->mb_enc) . '-';
		}
/*-- END HYPHENATION --*/


		// if it looks like we didn't finish any words for this chunk
		if ( count( $words ) == 1 ) {
		  // TO correct for error when word too wide for page - but only when one long word from left to right margin
		  if (count($content) == 1 && $currContent != ' ') {

/////////////////////////////////////////////////////////////////////////////////////


			$lastchar = mb_substr($words[0],mb_strlen($words[0], $this->mb_enc)-1, 1, $this->mb_enc);
/*-- CJK-FONTS --*/
			// mPDF 5.3.07
			if ($this->checkCJK) {
			   // Last character that fits is not allowed to end a line - move lastchar(s) to start of next line
			   if (!$is_table && preg_match("/[".$this->CJKleading."]/u", $lastchar)) {
				//move lastchar(s) to next line
				$m0 = $lastchar;
				$m1 = $c;
				while(preg_match("/[".$this->CJKleading."]/u", $m0) && mb_strlen($words[0], $this->mb_enc)>2) {
					// trim last letter off word[0]
					$words[0] = mb_substr($words[0],0,mb_strlen($words[0], $this->mb_enc)-1, $this->mb_enc);
					// and add it to savedContent for next line
					$savedContent = $m0.$savedContent;
					$m1 = $lastchar;
					$lastchar = mb_substr($words[0],mb_strlen($words[0], $this->mb_enc)-1, 1, $this->mb_enc);
					$m0 = $lastchar;
				}
			   }
			   // Next character is not allowed to start a new line - move lastchar(s) to next line
			   else if (!$is_table && preg_match("/[".$this->CJKfollowing."]/u", $c)) {
				// try squeezing another character(s) onto this line = Oikomi
				if ($this->allowCJKorphans) {
				      $lookahead = mb_substr($s,$i+1,1,$this->mb_enc );
					//if lookahead is not another following char
					if ($lookahead && !preg_match("/[".$this->CJKfollowing."]/u", $lookahead)) {
						$currContent .= $c;
						$cutoffWidth = $contentWidth;
						$contentWidth += $cw;
						if ($this->allowCJKoverflow && !$is_table && preg_match("/[".$this->CJKoverflow."]/u", $c)) { $CJKoverflow = true; }
						continue;
					}
				}
				// or move lastchar(s) to next line to keep $c company = Oidashi
				$m0 = $lastchar;
				$m1 = $c;
				while(preg_match("/[".$this->CJKfollowing."]/u", $m1) && mb_strlen($words[0], $this->mb_enc)>2) {
					// trim last letter off word[0]
					$words[0] = mb_substr($words[0],0,mb_strlen($words[0], $this->mb_enc)-1, $this->mb_enc);
					// and add it to savedContent for next line
					$savedContent = $m0.$savedContent;
					$m1 = $lastchar;
					$lastchar = mb_substr($words[0],mb_strlen($words[0], $this->mb_enc)-1, 1, $this->mb_enc);
					$m0 = $lastchar;
				}

			   }
			}
/*-- END CJK-FONTS --*/

			$lastContent = $words[0]; 
			$savedFont = $this->saveFont();
			// replace the current content with the cropped version
			$currContent = rtrim( $lastContent );
		  }
		  else {
			// save and crop off the content currently on the stack
			$savedContent = array_pop( $content );
			$savedContentB = $contentB[(count($contentB)-1)];	// mPDF 5.3.61
			$savedFont = array_pop( $font );
			// trim any trailing spaces off the last bit of content
			$currContent =& $content[ count( $content ) - 1 ];
			$currContent = rtrim( $currContent );
		  }
		}
		else {	// otherwise, we need to find which bit to cut off
             $lastContent = '';
              for ( $w = 0; $w < count( $words ) - 1; $w++) { $lastContent .= $words[ $w ]." "; }
              $savedContent = $words[ count( $words ) - 1 ];
              $savedFont = $this->saveFont();
              // replace the current content with the cropped version
              $currContent = rtrim( $lastContent );
		}
		// CJK - strip CJK space at end of line
		// &#x3000; = \xe3\x80\x80 = CJK space
		if ($this->checkCJK) { $currContent = preg_replace("/\xe3\x80\x80$/",'',$currContent) ; }	// *CJK-FONTS*


		if (isset($this->objectbuffer[(count($content)-1)]) && $this->objectbuffer[(count($content)-1)]['type']=='dottab') {
			$savedObj = array_pop( $this->objectbuffer );
			$contentWidth -= ($this->objectbuffer[(count($content)-1)]['OUTER-WIDTH'] * _MPDFK);
		}

		// Set Current lineheight (correction factor)
		$lhfixed = false; 
/*-- LISTS --*/
		if ($is_list) {
			if (preg_match('/([0-9.,]+)mm/',$this->list_lineheight[$this->listlvl][$this->listOcc],$am)) { 
				$lhfixed = true; 
				$def_fontsize = $this->InlineProperties['LISTITEM'][$this->listlvl][$this->listOcc][$this->listnum]['size'];
				$this->lineheight_correction = $am[1] / $def_fontsize ;
			}
			else { 
				$this->lineheight_correction = $this->list_lineheight[$this->listlvl][$this->listOcc]; 
			}
		}
		else
/*-- END LISTS --*/
/*-- TABLES --*/
		if ($is_table) {
			if (preg_match('/([0-9.,]+)mm/',$this->table_lineheight,$am)) { 
				$lhfixed = true; 
				$def_fontsize = $this->FontSize; 				// needs to be default font-size for block ****
				$this->lineheight_correction = $lineHeight / $def_fontsize ; 
			}
			else { 
				$this->lineheight_correction = $this->table_lineheight; 
			}
		}
		else
/*-- END TABLES --*/
		if (isset($this->blk[$this->blklvl]['line_height']) && $this->blk[$this->blklvl]['line_height']) {
			if (preg_match('/([0-9.,]+)mm/',$this->blk[$this->blklvl]['line_height'],$am)) { 
				$lhfixed = true; 
				$def_fontsize = $this->blk[$this->blklvl]['InlineProperties']['size']; 	// needs to be default font-size for block ****
				$this->lineheight_correction = $am[1] / $def_fontsize ;
			}
			else { 
				$this->lineheight_correction = $this->blk[$this->blklvl]['line_height']; 
			}
		} 
		else {
			$this->lineheight_correction = $this->normalLineheight; 
		}
		// update $contentWidth and $cutoffWidth since they changed with cropping
		// Also correct lineheight to maximum fontsize (not for tables)
		$contentWidth = 0;
		//  correct lineheight to maximum fontsize
		if ($lhfixed) { $maxlineHeight = $this->lineheight; }
		else { $maxlineHeight = 0; }
		$this->forceExactLineheight = true;
		$maxfontsize = 0;
		// While we're at it, check for cursive text
		$checkCursive=false;
		if ($this->biDirectional) {  $checkCursive=true; }	// *RTL*
		foreach ( $content as $k => $chunk )
		{
              $this->restoreFont( $font[ $k ]);
		  if (!isset($this->objectbuffer[$k])) { 
			if (!$this->usingCoreFont) {
			      $content[$k] = $chunk = str_replace("\xc2\xad",'',$chunk ); 
				if (isset($this->CurrentFont['indic']) && $this->CurrentFont['indic']) {  $checkCursive=true; }	// *INDIC*
			}
			// Soft Hyphens chr(173)
			else if ($this->FontFamily!='csymbol' && $this->FontFamily!='czapfdingbats') {
			      $content[$k] = $chunk = str_replace(chr(173),'',$chunk );
			}
			$contentWidth += $this->GetStringWidth( $chunk ) * _MPDFK; 
			// mPDF 5.3.61
			if (!empty($this->spanborddet)) { 
				if (strpos($contentB[$k],'L')!==false) $contentWidth += $this->spanborddet['L']['w'] * _MPDFK; 
				if (strpos($contentB[$k],'R')!==false) $contentWidth += $this->spanborddet['R']['w'] * _MPDFK; 
			}
			if (!$lhfixed) { $maxlineHeight = max($maxlineHeight,$this->FontSize * $this->lineheight_correction ); }
			if ($lhfixed && ($this->FontSize > $def_fontsize || ($this->FontSize > ($lineHeight * $this->lineheight_correction) && $is_list))) { 
				$this->forceExactLineheight = false; 
			}
			$maxfontsize = max($maxfontsize,$this->FontSize); 
		  }
		}

		$lastfontreqstyle = $font[count($font)-1]['ReqFontStyle'];
		$lastfontstyle = $font[count($font)-1]['style'];
		if ($blockdir == 'ltr' && strpos($lastfontreqstyle,"I") !== false && strpos($lastfontstyle,"I") === false) {	// Artificial italic
			$lastitalic = $this->FontSize*0.15*_MPDFK;
		}
		else { $lastitalic = 0; }


/*-- LISTS --*/
		if ($is_list && is_array($this->bulletarray) && $this->bulletarray) {
	  		$actfs = $this->bulletarray['fontsize'];
			if (!$lhfixed) { $maxlineHeight = max($maxlineHeight,$actfs * $this->lineheight_correction );  }
			if ($lhfixed && $actfs > $def_fontsize) { $this->forceExactLineheight = false; }
			$maxfontsize = max($maxfontsize,$actfs);
		}
/*-- END LISTS --*/

		// when every text item checked i.e. $maxfontsize is set properly

		$af = 0; 	// Above font
		$bf = 0; 	// Below font
		$mta = 0;	// Maximum top-aligned 
		$mba = 0;	// Maximum bottom-aligned 

		foreach ( $content as $k => $chunk ) {
		  if (isset($this->objectbuffer[$k]) && $this->objectbuffer[$k]) { 
			$contentWidth += $this->objectbuffer[$k]['OUTER-WIDTH'] * _MPDFK; 
			$oh = $this->objectbuffer[$k]['OUTER-HEIGHT'];
			$va = $this->objectbuffer[$k]['vertical-align']; // = $objattr['vertical-align'] = set as M,T,B,S
			if ($lhfixed && $oh > $def_fontsize) { $this->forceExactLineheight = false; }

			if ($va == 'BS') {	//  (BASELINE default)
				$af = max($af, ($oh - ($maxfontsize * (0.5 + $this->baselineC))));
			}
			else if ($va == 'M') { 
				$af = max($af, ($oh - $maxfontsize)/2);
				$bf = max($bf, ($oh - $maxfontsize)/2);
			}
			else if ($va == 'TT') { 
				$bf = max($bf, ($oh - $maxfontsize));
			}
			else if ($va == 'TB') { 
				$af = max($af, ($oh - $maxfontsize));
			}
			else if ($va == 'T') { 
				$mta = max($mta, $oh);
			}
			else if ($va == 'B') { 
				$mba = max($mba, $oh);
			}
		  }
		}
		if ((!$lhfixed || !$this->forceExactLineheight) && ($af > (($maxlineHeight - $maxfontsize)/2) || $bf > (($maxlineHeight - $maxfontsize)/2))) {
			$maxlineHeight = $maxfontsize + $af + $bf;
		}
		else if (!$lhfixed) { $af = $bf = ($maxlineHeight - $maxfontsize)/2; }

		if ($mta > $maxlineHeight) { 
			$bf += ($mta - $maxlineHeight);
			$maxlineHeight = $mta;
		}
		if ($mba > $maxlineHeight) { 
			$af += ($mba - $maxlineHeight);
			$maxlineHeight = $mba;
		}


		$lineHeight = $maxlineHeight; 
		$cutoffWidth = $contentWidth;
		// If NOT images, and maxfontsize NOT > lineHeight - this value determines text baseline positioning
		if ($lhfixed && $af==0 && $bf==0 && $maxfontsize<=($def_fontsize * $this->lineheight_correction * 0.8 )) { 
			$this->linemaxfontsize = $def_fontsize; 
		}
		else { $this->linemaxfontsize = $maxfontsize; }


		$inclCursive=false;
		foreach ( $content as $k => $chunk ) {
			if (!isset($this->objectbuffer[$k]) || (isset($this->objectbuffer[$k]) && !$this->objectbuffer[$k])) {
				if ($this->usingCoreFont) {
				      $content[$k] = str_replace(chr(160),chr(32),$chunk );
				}
				else {
				      $content[$k] = str_replace(chr(194).chr(160),chr(32),$chunk ); 
					if ($checkCursive) {
						if (preg_match("/([".$this->pregRTLchars."])/u", $chunk)) { $inclCursive = true; }	// *RTL*
						if (preg_match("/([".$this->pregHIchars.$this->pregBNchars.$this->pregPAchars."])/u", $chunk)) { $inclCursive = true; }	// *INDIC*
					}
				}
			}
		}

		// JUSTIFICATION J
		$jcharspacing = 0;
		$jws = 0;
		$nb_carac = 0;
		$nb_spaces = 0;
		// if it's justified, we need to find the char/word spacing (or if orphans have allowed length of line to go over the maxwidth)
		if(( $align == 'J' ) || (($cutoffWidth + $lastitalic > $maxWidth - $WidthCorrection - (($this->cMarginL+$this->cMarginR)*_MPDFK) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * _MPDFK) ) +  0.001) && !$CJKoverflow)) {   // 0.001 is to correct for deviations converting mm=>pts
		  // JUSTIFY J (Use character spacing)
 		  // WORD SPACING
			foreach ( $content as $k => $chunk ) {
				if (!isset($this->objectbuffer[$k]) || (isset($this->objectbuffer[$k]) && !$this->objectbuffer[$k])) {
					$nb_carac += mb_strlen( $chunk, $this->mb_enc ) ;  
					$nb_spaces += mb_substr_count( $chunk,' ', $this->mb_enc ) ;  
				}
			}
			list($jcharspacing,$jws) = $this->GetJspacing($nb_carac,$nb_spaces,($maxWidth-$lastitalic-$cutoffWidth-$WidthCorrection-(($this->cMarginL+$this->cMarginR)*_MPDFK)-($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * _MPDFK) )),$inclCursive);
		}


		// WORD SPACING
		$empty = $maxWidth - $lastitalic-$WidthCorrection - $contentWidth - (($this->cMarginL+$this->cMarginR)* _MPDFK) - ($paddingL+$paddingR +(($fpaddingL + $fpaddingR) * _MPDFK) );

		$empty -= ($jcharspacing * $nb_carac);
		$empty -= ($jws * $nb_spaces);

		$empty /= _MPDFK;
		$b = ''; //do not use borders
		// Get PAGEBREAK TO TEST for height including the top border/padding
		$check_h = max($this->divheight,$lineHeight);
		if (($newblock) && ($blockstate==1 || $blockstate==3) && ($this->blklvl > 0) && ($lineCount == 1) && (!$is_table) && (!$is_list)) { 
			$check_h += ($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['margin_top'] + $this->blk[$this->blklvl]['border_top']['w']);
		}

		if ($this->ColActive && $check_h > ($this->PageBreakTrigger - $this->y0)) { 
			$this->SetCol($this->NbCol-1);
		}

		// PAGEBREAK
		// 'If' below used in order to fix "first-line of other page with justify on" bug 
		if(!$is_table && ($this->y+$check_h) > $this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak()) {
			$bak_x=$this->x;//Current X position

			// WORD SPACING
			$ws=$this->ws;//Word Spacing
			$charspacing=$this->charspacing;//Character Spacing
			$this->ResetSpacing();

		      $this->AddPage($this->CurOrientation);

		      $this->x = $bak_x;
			// Added to correct for OddEven Margins
			$currentx += $this->MarginCorrection;
			$this->x += $this->MarginCorrection;

			// WORD SPACING
			$this->SetSpacing($charspacing,$ws);
		}

		if ($this->keep_block_together && !$is_table && $this->kt_p00 < $this->page && ($this->y+$check_h) > $this->kt_y00) {
			$this->printdivbuffer();
			$this->keep_block_together = 0;
		}


/*-- COLUMNS --*/
		// COLS
		// COLUMN CHANGE
		if ($this->CurrCol != $oldcolumn) {
			$currentx += $this->ChangeColumn * ($this->ColWidth+$this->ColGap);
			$this->x += $this->ChangeColumn * ($this->ColWidth+$this->ColGap);
			$oldcolumn = $this->CurrCol;
		}

		if ($this->ColActive && !$is_table) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*
/*-- END COLUMNS --*/

		// TOP MARGIN
		if (($newblock) && ($blockstate==1 || $blockstate==3) && ($this->blk[$this->blklvl]['margin_top']) && ($lineCount == 1) && (!$is_table) && (!$is_list)) { 
			$this->DivLn($this->blk[$this->blklvl]['margin_top'],$this->blklvl-1,true,$this->blk[$this->blklvl]['margin_collapse']); 
			if ($this->ColActive) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*
		}


		// Update y0 for top of block (used to paint border)
		if (($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 1) && (!$is_table) && (!$is_list)) { 
			$this->blk[$this->blklvl]['y0'] = $this->y;
			$this->blk[$this->blklvl]['startpage'] = $this->page;
		}

		// TOP PADDING and BORDER spacing/fill
		if (($newblock) && ($blockstate==1 || $blockstate==3) && (($this->blk[$this->blklvl]['padding_top']) || ($this->blk[$this->blklvl]['border_top'])) && ($lineCount == 1) && (!$is_table) && (!$is_list)) { 
			// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
			$this->DivLn($this->blk[$this->blklvl]['padding_top'] + $this->blk[$this->blklvl]['border_top']['w'],-3,true,false,1);
			if ($this->ColActive) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*
		}

		$arraysize = count($content);

		$margins = ($this->cMarginL+$this->cMarginR) + ($ipaddingL+$ipaddingR + $fpaddingR + $fpaddingR );
 
		// PAINT BACKGROUND FOR THIS LINE
		if (!$is_table) { $this->DivLn($lineHeight,$this->blklvl,false); }	// false -> don't advance y

		$this->x = $currentx + $this->cMarginL + $ipaddingL + $fpaddingL ;
		if ($align == 'R') { $this->x += $empty; }
		else if ($align == 'C') { $this->x += ($empty / 2); }

		// Paragraph INDENT
		if (isset($this->blk[$this->blklvl]['text_indent']) && ($newblock) && ($blockstate==1 || $blockstate==3) && ($lineCount == 1) && (!$is_table) && ($blockdir !='rtl') && ($align !='C')) { 
			$ti = $this->ConvertSize($this->blk[$this->blklvl]['text_indent'],$this->blk[$this->blklvl]['inner_width'],$this->FontSize,false); 
			$this->x += $ti; 
		}

		// DIRECTIONALITY RTL
		$all_rtl = false;
		$contains_rtl = false;
/*-- RTL --*/
   		if ($blockdir == 'rtl' || $this->biDirectional)  {
			$all_rtl = true;
			foreach ( $content as $k => $chunk ) {
				$reversed = $this->magic_reverse_dir($chunk, false, $blockdir);

				if ($reversed > 0) { $contains_rtl = true; }
				if ($reversed < 2) { $all_rtl = false; }
				$content[$k] = $chunk;
			}
			if (($blockdir =='rtl' && $contains_rtl) || $all_rtl) { 
				$content = array_reverse($content,false); 
				$contentB = array_reverse($contentB,false); 	// mPDF 5.3.61
			}
		}
/*-- END RTL --*/

		foreach ( $content as $k => $chunk ) {

			// FOR IMAGES - UPDATE POSITION
			if (($blockdir =='rtl' && $contains_rtl) || $all_rtl) { $dirk = $arraysize-1 - $k ; } else { $dirk = $k; }

			$va = 'M';	// default for text
			if (isset($this->objectbuffer[$dirk]) && $this->objectbuffer[$dirk]) {
			  $xadj = $this->x - $this->objectbuffer[$dirk]['OUTER-X'] ; 
			  $this->objectbuffer[$dirk]['OUTER-X'] += $xadj;
			  $this->objectbuffer[$dirk]['BORDER-X'] += $xadj;
			  $this->objectbuffer[$dirk]['INNER-X'] += $xadj;
			  $va = $this->objectbuffer[$dirk]['vertical-align'];
			  $yadj = $this->y - $this->objectbuffer[$dirk]['OUTER-Y'];
			  if ($va == 'BS') { 
				$yadj += $af + ($this->linemaxfontsize * (0.5 + $this->baselineC)) - $this->objectbuffer[$dirk]['OUTER-HEIGHT'];
			  }
			  else if ($va == 'M' || $va == '') { 
				$yadj += $af + ($this->linemaxfontsize /2) - ($this->objectbuffer[$dirk]['OUTER-HEIGHT']/2);
			  }
			  else if ($va == 'TB') { 
				$yadj += $af + $this->linemaxfontsize - $this->objectbuffer[$dirk]['OUTER-HEIGHT'];
			  }
			  else if ($va == 'TT') { 
				$yadj += $af;
			  }
			  else if ($va == 'B') { 
				$yadj += $af + $this->linemaxfontsize + $bf - $this->objectbuffer[$dirk]['OUTER-HEIGHT'];
			  }
			  else if ($va == 'T') { 
				$yadj += 0;
			  }
			  $this->objectbuffer[$dirk]['OUTER-Y'] += $yadj;
			  $this->objectbuffer[$dirk]['BORDER-Y'] += $yadj;
			  $this->objectbuffer[$dirk]['INNER-Y'] += $yadj;
			}

			// DIRECTIONALITY RTL
			if ((($blockdir == 'rtl') && ($contains_rtl )) || ($all_rtl )) { $this->restoreFont($font[$arraysize-1 - $k]); }
			else { $this->restoreFont( $font[ $k ] ); }

			$this->SetSpacing(($this->fixedlSpacing*_MPDFK)+$jcharspacing,($this->fixedlSpacing+$this->minwSpacing)*_MPDFK+$jws);
			// Now unset these values so they don't influence GetStringwidth below or in fn. Cell
			$this->fixedlSpacing = false;
			$this->minwSpacing = 0;

	 		// *********** SPAN BACKGROUND COLOR ***************** //
			if ($this->spanbgcolor) { 
				$cor = $this->spanbgcolorarray;
				$this->SetFColor($cor);
				$save_fill = $fill; $spanfill = 1; $fill = 1;
			}
			// mPDF 5.3.61
			if (!empty($this->spanborddet)) { 
				if (strpos($contentB[$k],'L')!==false) $this->x += $this->spanborddet['L']['w'];
				if (strpos($contentB[$k],'L')===false) $this->spanborddet['L']['s'] = $this->spanborddet['L']['w'] = 0; 
				if (strpos($contentB[$k],'R')===false) $this->spanborddet['R']['s'] = $this->spanborddet['R']['w'] = 0; 
			}

			// WORD SPACING
		      $stringWidth = $this->GetStringWidth($chunk );
			$stringWidth += ( $this->charspacing * mb_strlen($chunk,$this->mb_enc ) / _MPDFK );
			$stringWidth += ( $this->ws * mb_substr_count($chunk,' ',$this->mb_enc ) / _MPDFK );
			if (isset($this->objectbuffer[$dirk])) { $stringWidth = $this->objectbuffer[$dirk]['OUTER-WIDTH'];  }

			if ($stringWidth==0) { $stringWidth = 0.000001; }
			if ($k == $arraysize-1) {
				$stringWidth -= ( $this->charspacing / _MPDFK ); 
				$this->Cell( $stringWidth, $lineHeight, $chunk, '', 1, '', $fill, $this->HREF, $currentx,0,0,'M', $fill, $af, $bf, true ); //mono-style line or last part (skips line)	// mPDF 5.3.07
			}
			else $this->Cell( $stringWidth, $lineHeight, $chunk, '', 0, '', $fill, $this->HREF, 0, 0,0,'M', $fill, $af, $bf, true );//first or middle part	// mPDF 5.3.07

			// mPDF 5.3.61
			if (!empty($this->spanborddet)) { 
				if (strpos($contentB[$k],'R')!==false && $k != $arraysize-1)  $this->x += $this->spanborddet['R']['w'];
			}
	 		// *********** SPAN BACKGROUND COLOR OFF - RESET BLOCK BGCOLOR ***************** //
			if (isset($spanfill) && $spanfill) { 
				$fill = $save_fill; $spanfill = 0; 
				if ($fill) { $this->SetFColor($bcor); }
			}
		}
		if (!$is_table) { 
			$this->maxPosR = max($this->maxPosR , ($this->w - $this->rMargin - $this->blk[$this->blklvl]['outer_right_margin'])); 
			$this->maxPosL = min($this->maxPosL , ($this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'])); 
		}

		// move on to the next line, reset variables, tack on saved content and current char

		$this->printobjectbuffer($is_table, $blockdir);
		$this->objectbuffer = array();

/*-- LISTS --*/
		// LIST BULLETS/NUMBERS
		if ($is_list && is_array($this->bulletarray) && ($lineCount == 1) ) {
		  
		  $this->ResetSpacing();

		  $bull = $this->bulletarray;
	  	  if (isset($bull['level']) && isset($bull['occur']) && isset($this->InlineProperties['LIST'][$bull['level']][$bull['occur']])) { 
			$this->restoreInlineProperties($this->InlineProperties['LIST'][$bull['level']][$bull['occur']]); 
		  }
		  if (isset($bull['level']) && isset($bull['occur']) && isset($bull['num']) && isset($this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]) && $this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]) { $this->restoreInlineProperties($this->InlineProperties['LISTITEM'][$bull['level']][$bull['occur']][$bull['num']]); }
	  	  if (isset($bull['font']) && $bull['font'] == 'czapfdingbats') {
			$this->bullet = true;
			$this->SetFont('czapfdingbats','',$this->FontSizePt/2.5);
		  }
		  else { $this->SetFont($this->FontFamily,$this->FontStyle,$this->FontSizePt,true,true); }	// force output
	        //Output bullet
	  	  $this->x = $currentx;
	 	  if (isset($bull['x'])) { $this->x += $bull['x']; }
		  $this->y -= $lineHeight;
	  	  if (is_array($bull['col'])) { $this->SetTColor($bull['col']); }
		  if (isset($bull['txt'])) { $this->Cell($bull['w'], $lineHeight,$bull['txt'],'','',$bull['align'],0,'',0,-$this->cMarginL, -$this->cMarginR ); }
	  	  if (isset($bull['font']) && $bull['font'] == 'czapfdingbats') {
			$this->bullet = false;
		  }
		  $this->x = $currentx;	// Reset
		  $this->y += $lineHeight;

		  if ($this->ColActive && !$is_table) { $this->breakpoints[$this->CurrCol][] = $this->y; }	// *COLUMNS*

		  $this->bulletarray = array();	// prevents repeat of bullet/number if <li>....<br />.....</li>
		}
/*-- END LISTS --*/


/*-- CSS-IMAGE-FLOAT --*/
		// Update values if set to skipline
		if ($this->floatmargins) { $this->_advanceFloatMargins(); }
/*-- END CSS-IMAGE-FLOAT --*/

		// Reset lineheight
		$lineHeight = $this->divheight;
		$valign = 'M';

		$this->restoreFont( $savedFont );
		// mPDF 5.3.61
		$lbw = $rbw = 0;	// Border widths
		if (!empty($this->spanborddet)) { 
			$lbw = $this->spanborddet['L']['w'];
			$rbw = $this->spanborddet['R']['w'];
		}

		$font = array();
		$content = array();
		$contentB = array();	// mPDF 5.3.61
		$contentWidth = 0;
		if (!empty($savedObj)) {
			$this->objectbuffer[] = $savedObj;
			$font[] = $savedFont;
			$content[] = '';
			$contentB[] = '';
			$contentWidth += $savedObj['OUTER-WIDTH'] * _MPDFK;
		}
		$font[] = $savedFont;
		$content[] = $savedContent . $c;
		$contentB[] = $savedContentB ;	// mPDF 5.3.61
		$currContent =& $content[ (count($content)-1) ];
		// CJK - strip CJK space at end of line
		// &#x3000; = \xe3\x80\x80 = CJK space
		if ($this->checkCJK && $currContent == "\xe3\x80\x80") { $currContent = '' ; }	// *CJK-FONTS*
		$contentWidth += $this->GetStringWidth( $currContent ) * _MPDFK;
		// mPDF 5.3.61
		if (strpos($savedContentB,'L')!==false) $contentWidth += $lbw;
		$cutoffWidth = $contentWidth;
		$CJKoverflow = false;
      }
      // another character will fit, so add it on
	else {
		$contentWidth += $cw;
		$currContent .= $c;
	}
    }
    unset($content);
    unset($contentB);	// mPDF 5.3.61
}
//----------------------END OF FLOWING BLOCK------------------------------------//


/*-- CSS-IMAGE-FLOAT --*/
// Update values if set to skipline
function _advanceFloatMargins() {
	// Update floatmargins - L
	if (isset($this->floatmargins['L']) && $this->floatmargins['L']['skipline'] && $this->floatmargins['L']['y0'] != $this->y) {
		$yadj = $this->y - $this->floatmargins['L']['y0'];
		$this->floatmargins['L']['y0'] = $this->y;
		$this->floatmargins['L']['y1'] += $yadj;

		// Update objattr in floatbuffer
		if ($this->floatbuffer[$this->floatmargins['L']['id']]['border_left']['w']) {
			$this->floatbuffer[$this->floatmargins['L']['id']]['BORDER-Y'] += $yadj;
		}
		$this->floatbuffer[$this->floatmargins['L']['id']]['INNER-Y'] += $yadj;
		$this->floatbuffer[$this->floatmargins['L']['id']]['OUTER-Y'] += $yadj;

		// Unset values
		$this->floatbuffer[$this->floatmargins['L']['id']]['skipline'] = false;
		$this->floatmargins['L']['skipline'] = false;
		$this->floatmargins['L']['id'] = '';
	}
	// Update floatmargins - R
	if (isset($this->floatmargins['R']) && $this->floatmargins['R']['skipline'] && $this->floatmargins['R']['y0'] != $this->y) {
		$yadj = $this->y - $this->floatmargins['R']['y0'];
		$this->floatmargins['R']['y0'] = $this->y;
		$this->floatmargins['R']['y1'] += $yadj;

		// Update objattr in floatbuffer
		if ($this->floatbuffer[$this->floatmargins['R']['id']]['border_left']['w']) {
			$this->floatbuffer[$this->floatmargins['R']['id']]['BORDER-Y'] += $yadj;
		}
		$this->floatbuffer[$this->floatmargins['R']['id']]['INNER-Y'] += $yadj;
		$this->floatbuffer[$this->floatmargins['R']['id']]['OUTER-Y'] += $yadj;

		// Unset values
		$this->floatbuffer[$this->floatmargins['R']['id']]['skipline'] = false;
		$this->floatmargins['R']['skipline'] = false;
		$this->floatmargins['R']['id'] = '';
	}
}
/*-- END CSS-IMAGE-FLOAT --*/



////////////////////////////////////////////////////////////////////////////////
// ADDED forcewrap - to call from inline OBJECT functions to breakwords if necessary in cell
////////////////////////////////////////////////////////////////////////////////
function WordWrap(&$text, $maxwidth, $forcewrap = 0) {
    $biggestword=0;
    $toonarrow=false;

    $text = trim($text);

    if ($text==='') return 0;
    $space = $this->GetCharWidth(' ',false);	// mPDF 5.3.04
    $lines = explode("\n", $text);
    $text = '';
    $count = 0;
    foreach ($lines as $line) {
	$words = explode(' ', $line);
	$width = 0;
	foreach ($words as $word) {
		$word = trim($word);
		$wordwidth = $this->GetStringWidth($word);
		//Warn user that maxwidth is insufficient
		if ($wordwidth > $maxwidth + 0.0001) {
			if ($wordwidth > $biggestword) { $biggestword = $wordwidth; }
			$toonarrow=true;
			// ADDED
			if ($forcewrap) {
			  while($wordwidth > $maxwidth) {
				$chw = 0;	// check width
				for ( $i = 0; $i < mb_strlen($word, $this->mb_enc ); $i++ ) {
					$chw = $this->GetStringWidth(mb_substr($word,0,$i+1,$this->mb_enc ));
					if ($chw > $maxwidth ) {
						if ($text) {
							$text = rtrim($text)."\n".mb_substr($word,0,$i,$this->mb_enc );
							$count++;
						}
						else {
							$text = mb_substr($word,0,$i,$this->mb_enc );
						}
						$word = mb_substr($word,$i,mb_strlen($word, $this->mb_enc )-$i,$this->mb_enc );
						$wordwidth = $this->GetStringWidth($word);
						$width = $maxwidth; 
						break;
					}
				}
			  }
			}
		}

		if ($width + $wordwidth  < $maxwidth - 0.0001) {
			$width += $wordwidth + $space;
			$text .= $word.' ';
		}
		else {
			$width = $wordwidth + $space;
			$text = rtrim($text)."\n".$word.' ';
			$count++;
            }
	}
	$text .= "\n";
	$count++;
    }
    $text = rtrim($text);

    //Return -(wordsize) if word is bigger than maxwidth 

	// ADDED
	if ($forcewrap) { return $count; }
      if (($toonarrow) && ($this->table_error_report)) {
		$this->Error("Word is too long to fit in table - ".$this->table_error_report_param); 
	}
    if ($toonarrow) return -$biggestword;
    else return $count;
}

/*-- END HTML-CSS --*/

function _SetTextRendering($mode) { 
	if (!(($mode == 0) || ($mode == 1) || ($mode == 2))) 
	$this->Error("Text rendering mode should be 0, 1 or 2 (value : $mode)"); 
	$tr = ($mode.' Tr'); 
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['TextRendering']) && $this->pageoutput[$this->page]['TextRendering'] != $tr) || !isset($this->pageoutput[$this->page]['TextRendering']) || $this->keep_block_together)) { $this->_out($tr); }
	$this->pageoutput[$this->page]['TextRendering'] = $tr;

} 

function SetTextOutline($width, $col=0) {
  if ($width == false) //Now resets all values
  { 
    $this->outline_on = false;
    $this->SetLineWidth(0.2); 
    $this->SetDColor($this->ConvertColor(0));
    $this->_SetTextRendering(0); 
    $tr = ('0 Tr'); 
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['TextRendering']) && $this->pageoutput[$this->page]['TextRendering'] != $tr) || !isset($this->pageoutput[$this->page]['TextRendering']) || $this->keep_block_together)) { $this->_out($tr); }
	$this->pageoutput[$this->page]['TextRendering'] = $tr;
  }
  else
  { 
    $this->SetLineWidth($width); 
    $this->SetDColor($col);
    $tr = ('2 Tr'); 
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['TextRendering']) && $this->pageoutput[$this->page]['TextRendering'] != $tr) || !isset($this->pageoutput[$this->page]['TextRendering']) || $this->keep_block_together)) { $this->_out($tr); }
	$this->pageoutput[$this->page]['TextRendering'] = $tr;
  } 
}

function Image($file,$x,$y,$w=0,$h=0,$type='',$link='',$paint=true, $constrain=true, $watermark=false, $shownoimg=true, $allowvector=true) {
	$orig_srcpath = $file;
	$this->GetFullPath($file);

	$info=$this->_getImage($file, true, $allowvector, $orig_srcpath );
	if(!$info && $paint) {
		$info = $this->_getImage($this->noImageFile);
		if ($info) { 
			$file = $this->noImageFile; 
			$w = ($info['w'] * (25.4/$this->dpi)); 	// 14 x 16px
			$h = ($info['h'] * (25.4/$this->dpi)); 	// 14 x 16px
		}
	}
	if(!$info) return false;
	//Automatic width and height calculation if needed
	if($w==0 and $h==0) {
/*-- IMAGES-WMF --*/
           if ($info['type']=='wmf') { 
			// WMF units are twips (1/20pt)
			// divide by 20 to get points
			// divide by k to get user units
			$w = abs($info['w'])/(20*_MPDFK);
			$h = abs($info['h']) / (20*_MPDFK);
		}
		else 
/*-- END IMAGES-WMF --*/
           if ($info['type']=='svg') { 
			// returned SVG units are pts
			// divide by k to get user units (mm)
			$w = abs($info['w'])/_MPDFK;
			$h = abs($info['h']) /_MPDFK;
		}
		else {
			//Put image at default image dpi
			$w=($info['w']/_MPDFK) * (72/$this->img_dpi);
			$h=($info['h']/_MPDFK) * (72/$this->img_dpi);
		}
	}
	if($w==0)	$w=abs($h*$info['w']/$info['h']); 
	if($h==0)	$h=abs($w*$info['h']/$info['w']); 

/*-- WATERMARK --*/
	if ($watermark) {
	  $maxw = $this->w;
	  $maxh = $this->h;
	  // Size = D PF or array
	  if (is_array($this->watermark_size)) {
		$w = $this->watermark_size[0];
		$h = $this->watermark_size[1];
	  }
	  else if (!is_string($this->watermark_size)) {
		$maxw -= $this->watermark_size*2;
		$maxh -= $this->watermark_size*2;
		$w = $maxw;
		$h=abs($w*$info['h']/$info['w']);
		if ($h > $maxh )  {
			$h = $maxh ; $w=abs($h*$info['w']/$info['h']);
		}
	  }
	  else if ($this->watermark_size == 'F') {
		if ($this->ColActive) { $maxw = $this->w - ($this->DeflMargin + $this->DefrMargin); }
		else { $maxw = $this->pgwidth; }
		$maxh = $this->h - ($this->tMargin + $this->bMargin);
		$w = $maxw;
		$h=abs($w*$info['h']/$info['w']);
		if ($h > $maxh )  {
			$h = $maxh ; $w=abs($h*$info['w']/$info['h']);
		}
	  }
	  else  if ($this->watermark_size == 'P') {	// Default P
		$w = $maxw;
		$h=abs($w*$info['h']/$info['w']);
		if ($h > $maxh )  {
			$h = $maxh ; $w=abs($h*$info['w']/$info['h']);
		}
	  }
	  // Automatically resize to maximum dimensions of page if too large
	  if ($w > $maxw) {
		$w = $maxw;
		$h=abs($w*$info['h']/$info['w']);
	  }
	  if ($h > $maxh )  {
		$h = $maxh ;
		$w=abs($h*$info['w']/$info['h']);
	  }
	  // Position
	  if (is_array($this->watermark_pos)) {
		$x = $this->watermark_pos[0];
		$y = $this->watermark_pos[1];
	  }
	  else if ($this->watermark_pos == 'F')  {	// centred on printable area
		if ($this->ColActive) {	// *COLUMNS*
			if (($this->mirrorMargins) && (($this->page)%2==0)) { $xadj = $this->DeflMargin-$this->DefrMargin; }	// *COLUMNS*
			else { $xadj = 0; }	// *COLUMNS*
			$x = ($this->DeflMargin - $xadj + ($this->w - ($this->DeflMargin + $this->DefrMargin))/2) - ($w/2);	// *COLUMNS*
		}	// *COLUMNS*
		else { 	// *COLUMNS*
			$x = ($this->lMargin + ($this->pgwidth)/2) - ($w/2);
		}	// *COLUMNS*
		$y = ($this->tMargin + ($this->h - ($this->tMargin + $this->bMargin))/2) - ($h/2);
	  }
	  else {	// default P - centred on whole page
		$x = ($this->w/2) - ($w/2);
		$y = ($this->h/2) - ($h/2);
	  }
/*-- IMAGES-WMF --*/
	  if ($info['type']=='wmf') { 
		$sx = $w*_MPDFK / $info['w'];
		$sy = -$h*_MPDFK / $info['h'];
		$outstring = sprintf('q %.3F 0 0 %.3F %.3F %.3F cm /FO%d Do Q', $sx, $sy, $x*_MPDFK-$sx*$info['x'], (($this->h-$y)*_MPDFK)-$sy*$info['y'], $info['i']);
	  }
	  else  
/*-- END IMAGES-WMF --*/
	  if ($info['type']=='svg') { 
		$sx = $w*_MPDFK / $info['w'];
		$sy = -$h*_MPDFK / $info['h'];
		$outstring = sprintf('q %.3F 0 0 %.3F %.3F %.3F cm /FO%d Do Q', $sx, $sy, $x*_MPDFK-$sx*$info['x'], (($this->h-$y)*_MPDFK)-$sy*$info['y'], $info['i']);
	  }
	  else { 
		$outstring = sprintf("q %.3F 0 0 %.3F %.3F %.3F cm /I%d Do Q",$w*_MPDFK,$h*_MPDFK,$x*_MPDFK,($this->h-($y+$h))*_MPDFK,$info['i']);
	  }

	  if ($this->watermarkImgBehind) { 
		$outstring = $this->watermarkImgAlpha . "\n" . $outstring . "\n" . $this->SetAlpha(1, 'Normal', true) . "\n";
		$this->pages[$this->page] = preg_replace('/(___BACKGROUND___PATTERNS'.date('jY').')/', "\n".$outstring."\n".'\\1', $this->pages[$this->page]);
	  }
	  else { $this->_out($outstring); }

	  return 0;
	}	// end of IF watermark
/*-- END WATERMARK --*/

	if ($constrain) {
	  // Automatically resize to maximum dimensions of page if too large
	  if (isset($this->blk[$this->blklvl]['inner_width']) && $this->blk[$this->blklvl]['inner_width']) { $maxw = $this->blk[$this->blklvl]['inner_width']; }
	  else { $maxw = $this->pgwidth; }
	  if ($w > $maxw) {
		$w = $maxw;
		$h=abs($w*$info['h']/$info['w']);
	  }
	  if ($h > $this->h - ($this->tMargin + $this->bMargin + 1))  {  // see below - +10 to avoid drawing too close to border of page
   		$h = $this->h - ($this->tMargin + $this->bMargin + 1) ;
		if ($this->fullImageHeight) { $h = $this->fullImageHeight; }
		$w=abs($h*$info['w']/$info['h']);
	  }


	  //Avoid drawing out of the paper(exceeding width limits).
	  //if ( ($x + $w) > $this->fw ) {
	  if ( ($x + $w) > $this->w ) {
		$x = $this->lMargin;
		$y += 5;
	  }

	  $changedpage = false;
	  $oldcolumn = $this->CurrCol;
	  //Avoid drawing out of the page.
	  if($y+$h>$this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak()) {
		$this->AddPage($this->CurOrientation);
		// Added to correct for OddEven Margins
		$x=$x +$this->MarginCorrection;
		$y = $tMargin + $this->margin_header;
		$changedpage = true;
	  }
/*-- COLUMNS --*/
	  // COLS
	  // COLUMN CHANGE
	  if ($this->CurrCol != $oldcolumn) {
		$y = $this->y0;
		$x += $this->ChangeColumn * ($this->ColWidth+$this->ColGap);
		$this->x += $this->ChangeColumn * ($this->ColWidth+$this->ColGap);
	  }
/*-- END COLUMNS --*/
	}	// end of IF constrain

/*-- IMAGES-WMF --*/
	if ($info['type']=='wmf') { 
		$sx = $w*_MPDFK / $info['w'];
		$sy = -$h*_MPDFK / $info['h'];
		$outstring = sprintf('q %.3F 0 0 %.3F %.3F %.3F cm /FO%d Do Q', $sx, $sy, $x*_MPDFK-$sx*$info['x'], (($this->h-$y)*_MPDFK)-$sy*$info['y'], $info['i']);
	}
	else  
/*-- END IMAGES-WMF --*/
	if ($info['type']=='svg') { 
		$sx = $w*_MPDFK / $info['w'];
		$sy = -$h*_MPDFK / $info['h'];
		$outstring = sprintf('q %.3F 0 0 %.3F %.3F %.3F cm /FO%d Do Q', $sx, $sy, $x*_MPDFK-$sx*$info['x'], (($this->h-$y)*_MPDFK)-$sy*$info['y'], $info['i']);
	}
	else { 
		$outstring = sprintf("q %.3F 0 0 %.3F %.3F %.3F cm /I%d Do Q",$w*_MPDFK,$h*_MPDFK,$x*_MPDFK,($this->h-($y+$h))*_MPDFK,$info['i']);
	}

	if($paint) {
		$this->_out($outstring);
		if($link) $this->Link($x,$y,$w,$h,$link);

		// Avoid writing text on top of the image. // THIS WAS OUTSIDE THE if ($paint) bit!!!!!!!!!!!!!!!!
		$this->y = $y + $h;
	}

	//Return width-height array
	$sizesarray['WIDTH'] = $w;
	$sizesarray['HEIGHT'] = $h;
	$sizesarray['X'] = $x; //Position before painting image
	$sizesarray['Y'] = $y; //Position before painting image
	$sizesarray['OUTPUT'] = $outstring;

	$sizesarray['IMAGE_ID'] = $info['i'];
	$sizesarray['itype'] = $info['type'];
	$sizesarray['set-dpi'] = $info['set-dpi'];
	return $sizesarray;
}



//=============================================================
//=============================================================
//=============================================================
//=============================================================
//=============================================================
/*-- HTML-CSS --*/

function _getObjAttr($t) {
	$c = explode("\xbb\xa4\xac",$t,2);
	$c = explode(",",$c[1],2);
	foreach($c as $v) {
		$v = explode("=",$v,2);
		$sp[$v[0]] = $v[1];
	}
	return (unserialize($sp['objattr']));
}


function inlineObject($type,$x,$y,$objattr,$Lmargin,$widthUsed,$maxWidth,$lineHeight,$paint=false,$is_table=false)
{
   if ($is_table) { $k = $this->shrin_k; } else { $k = 1; }

   // NB $x is only used when paint=true
	// Lmargin not used
   $w = 0; 
   if (isset($objattr['width'])) { $w = $objattr['width']/$k; }
   $h = 0;
   if (isset($objattr['height'])) { $h = abs($objattr['height']/$k); }
   $widthLeft = $maxWidth - $widthUsed;
   $maxHeight = $this->h - ($this->tMargin + $this->bMargin + 10) ;
   if ($this->fullImageHeight) { $maxHeight = $this->fullImageHeight; }
   // For Images
   if (isset($objattr['border_left'])) {
	$extraWidth = ($objattr['border_left']['w'] + $objattr['border_right']['w'] + $objattr['margin_left']+ $objattr['margin_right'])/$k;
	$extraHeight = ($objattr['border_top']['w'] + $objattr['border_bottom']['w'] + $objattr['margin_top']+ $objattr['margin_bottom'])/$k;

	if ($type == 'image' || $type == 'barcode' || $type == 'textcircle') {	// mPDF 5.3.A5
		$extraWidth += ($objattr['padding_left'] + $objattr['padding_right'])/$k;
		$extraHeight += ($objattr['padding_top'] + $objattr['padding_bottom'])/$k;
	}
   }

   if (!isset($objattr['vertical-align'])) { $objattr['vertical-align'] = 'M'; }

   if ($type == 'image' || (isset($objattr['subtype']) && $objattr['subtype'] == 'IMAGE')) {
    if (isset($objattr['itype']) && ($objattr['itype'] == 'wmf' || $objattr['itype'] == 'svg')) {
	$file = $objattr['file'];
 	$info=$this->formobjects[$file];
    }
    else if (isset($objattr['file'])) {
	$file = $objattr['file'];
	$info=$this->images[$file];
    }
   }
    if ($type == 'annot' || $type == 'bookmark' || $type == 'indexentry' || $type == 'toc') {
	$w = 0.00001;
	$h = 0.00001;
   }

   // TEST whether need to skipline
   if (!$paint) {
	if ($type == 'hr') {	// always force new line
		if (($y + $h + $lineHeight > $this->PageBreakTrigger) && !$this->InFooter && !$is_table) { return array(-2, $w ,$h ); } // New page + new line
		else { return array(1, $w ,$h ); } // new line
	}
	else {
		if ($widthUsed > 0 && $w > $widthLeft && (!$is_table || $type != 'image')) { 	// New line needed
			if (($y + $h + $lineHeight > $this->PageBreakTrigger) && !$this->InFooter) { return array(-2,$w ,$h ); } // New page + new line
			return array(1,$w ,$h ); // new line
		}
		else if ($widthUsed > 0 && $w > $widthLeft && $is_table) { 	// New line needed in TABLE
			return array(1,$w ,$h ); // new line
		}
		// Will fit on line but NEW PAGE REQUIRED
		else if (($y + $h > $this->PageBreakTrigger) && !$this->InFooter && !$is_table) { return array(-1,$w ,$h ); }
		else { return array(0,$w ,$h ); }
	}
   }

   if ($type == 'annot' || $type == 'bookmark' || $type == 'indexentry' || $type == 'toc') {
	$w = 0.00001;
	$h = 0.00001;
	$objattr['BORDER-WIDTH'] = 0;
	$objattr['BORDER-HEIGHT'] = 0;
	$objattr['BORDER-X'] = $x;
	$objattr['BORDER-Y'] = $y;
	$objattr['INNER-WIDTH'] = 0;
	$objattr['INNER-HEIGHT'] = 0;
	$objattr['INNER-X'] = $x;
	$objattr['INNER-Y'] = $y;
  }

  if ($type == 'image') {
	// Automatically resize to width remaining
	if ($w > $widthLeft  && !$is_table) {
		$w = $widthLeft ;
		$h=abs($w*$info['h']/$info['w']);
	}
	$img_w = $w - $extraWidth ;
	$img_h = $h - $extraHeight ;

	$objattr['BORDER-WIDTH'] = $img_w + $objattr['padding_left']/$k + $objattr['padding_right']/$k + (($objattr['border_left']['w']/$k + $objattr['border_right']['w']/$k)/2) ;
	$objattr['BORDER-HEIGHT'] = $img_h + $objattr['padding_top']/$k + $objattr['padding_bottom']/$k + (($objattr['border_top']['w']/$k + $objattr['border_bottom']['w']/$k)/2) ;
	$objattr['BORDER-X'] = $x + $objattr['margin_left']/$k + (($objattr['border_left']['w']/$k)/2) ;
	$objattr['BORDER-Y'] = $y + $objattr['margin_top']/$k + (($objattr['border_top']['w']/$k)/2) ;
	$objattr['INNER-WIDTH'] = $img_w;
	$objattr['INNER-HEIGHT'] = $img_h;
	$objattr['INNER-X'] = $x + $objattr['padding_left']/$k + $objattr['margin_left']/$k + ($objattr['border_left']['w']/$k);
	$objattr['INNER-Y'] = $y + $objattr['padding_top']/$k + $objattr['margin_top']/$k + ($objattr['border_top']['w']/$k) ;
	$objattr['ID'] = $info['i'];
   }

   if ($type == 'input' && $objattr['subtype'] == 'IMAGE') { 
	$img_w = $w - $extraWidth ;
	$img_h = $h - $extraHeight ;
	$objattr['BORDER-WIDTH'] = $img_w + (($objattr['border_left']['w']/$k + $objattr['border_right']['w']/$k)/2) ;
	$objattr['BORDER-HEIGHT'] = $img_h + (($objattr['border_top']['w']/$k + $objattr['border_bottom']['w']/$k)/2) ;
	$objattr['BORDER-X'] = $x + $objattr['margin_left']/$k + (($objattr['border_left']['w']/$k)/2) ;
	$objattr['BORDER-Y'] = $y + $objattr['margin_top']/$k + (($objattr['border_top']['w']/$k)/2) ;
	$objattr['INNER-WIDTH'] = $img_w;
	$objattr['INNER-HEIGHT'] = $img_h;
	$objattr['INNER-X'] = $x + $objattr['margin_left']/$k + ($objattr['border_left']['w']/$k);
	$objattr['INNER-Y'] = $y + $objattr['margin_top']/$k + ($objattr['border_top']['w']/$k) ;
	$objattr['ID'] = $info['i'];
   }

  if ($type == 'barcode' || $type == 'textcircle') {	// mPDF 5.3.A5
	$b_w = $w - $extraWidth ;
	$b_h = $h - $extraHeight ;
	$objattr['BORDER-WIDTH'] = $b_w + $objattr['padding_left']/$k + $objattr['padding_right']/$k + (($objattr['border_left']['w']/$k + $objattr['border_right']['w']/$k)/2) ;
	$objattr['BORDER-HEIGHT'] = $b_h + $objattr['padding_top']/$k + $objattr['padding_bottom']/$k + (($objattr['border_top']['w']/$k + $objattr['border_bottom']['w']/$k)/2) ;
	$objattr['BORDER-X'] = $x + $objattr['margin_left']/$k + (($objattr['border_left']['w']/$k)/2) ;
	$objattr['BORDER-Y'] = $y + $objattr['margin_top']/$k + (($objattr['border_top']['w']/$k)/2) ;
	$objattr['INNER-X'] = $x + $objattr['padding_left']/$k + $objattr['margin_left']/$k + ($objattr['border_left']['w']/$k);
	$objattr['INNER-Y'] = $y + $objattr['padding_top']/$k + $objattr['margin_top']/$k + ($objattr['border_top']['w']/$k) ;
	$objattr['INNER-WIDTH'] = $b_w;
	$objattr['INNER-HEIGHT'] = $b_h;
   }


   if ($type == 'textarea') {
	// Automatically resize to width remaining
	if ($w > $widthLeft && !$is_table) {
		$w = $widthLeft ;
	}
	if (($y + $h > $this->PageBreakTrigger) && !$this->InFooter) {
		$h=$this->h - $y - $this->bMargin;
	}
   }

   if ($type == 'hr') {
	if ($is_table) { 
		$objattr['INNER-WIDTH'] = $maxWidth * $objattr['W-PERCENT']/100; 
		$objattr['width'] = $objattr['INNER-WIDTH']; 
		$w = $maxWidth;
	}
	else { 
		if ($w>$maxWidth) { $w = $maxWidth; }
		$objattr['INNER-WIDTH'] = $w; 
		$w = $maxWidth;
	}
  }



   if (($type == 'select') || ($type == 'input' && ($objattr['subtype'] == 'TEXT' || $objattr['subtype'] == 'PASSWORD'))) {
	// Automatically resize to width remaining
	if ($w > $widthLeft && !$is_table) {
		$w = $widthLeft;
	}
   }

   if ($type == 'textarea' || $type == 'select' || $type == 'input') {
	if (isset($objattr['fontsize'])) $objattr['fontsize'] /= $k;
	if (isset($objattr['linewidth'])) $objattr['linewidth'] /= $k;
   }

   if (!isset($objattr['BORDER-Y'])) { $objattr['BORDER-Y'] = 0; }
   if (!isset($objattr['BORDER-X'])) { $objattr['BORDER-X'] = 0; }
   if (!isset($objattr['INNER-Y'])) { $objattr['INNER-Y'] = 0; }
   if (!isset($objattr['INNER-X'])) { $objattr['INNER-X'] = 0; }

   //Return width-height array
   $objattr['OUTER-WIDTH'] = $w;
   $objattr['OUTER-HEIGHT'] = $h;
   $objattr['OUTER-X'] = $x;
   $objattr['OUTER-Y'] = $y;
   return $objattr;
}

/*-- END HTML-CSS --*/

//=============================================================
//=============================================================
//=============================================================
//=============================================================
//=============================================================

function SetLineJoin($mode=0)
{
	$s=sprintf('%d j',$mode);
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['LineJoin']) && $this->pageoutput[$this->page]['LineJoin'] != $s) || !isset($this->pageoutput[$this->page]['LineJoin']) || $this->keep_block_together)) { $this->_out($s); }
	$this->pageoutput[$this->page]['LineJoin'] = $s;

}
function SetLineCap($mode=2) {
	$s=sprintf('%d J',$mode);
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['LineCap']) && $this->pageoutput[$this->page]['LineCap'] != $s) || !isset($this->pageoutput[$this->page]['LineCap']) || $this->keep_block_together)) { $this->_out($s); }
	$this->pageoutput[$this->page]['LineCap'] = $s;

}

function SetDash($black=false,$white=false)
{
        if($black and $white) $s=sprintf('[%.3F %.3F] 0 d',$black*_MPDFK,$white*_MPDFK);
        else $s='[] 0 d';
	if($this->page>0 && ((isset($this->pageoutput[$this->page]['Dash']) && $this->pageoutput[$this->page]['Dash'] != $s) || !isset($this->pageoutput[$this->page]['Dash']) || $this->keep_block_together)) { $this->_out($s); }
	$this->pageoutput[$this->page]['Dash'] = $s;

}

function SetDisplayPreferences($preferences) {
	// String containing any or none of /HideMenubar/HideToolbar/HideWindowUI/DisplayDocTitle/CenterWindow/FitWindow
    $this->DisplayPreferences .= $preferences;
}


function Ln($h='',$collapsible=0)
{
// Added collapsible to allow collapsible top-margin on new page
	//Line feed; default value is last cell height
	$this->x = $this->lMargin + $this->blk[$this->blklvl]['outer_left_margin'];
	if ($collapsible && ($this->y==$this->tMargin) && (!$this->ColActive)) { $h = 0; }
	if(is_string($h)) $this->y+=$this->lasth;
	else $this->y+=$h;
}

/*-- HTML-CSS --*/
// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
function DivLn($h,$level=-3,$move_y=true,$collapsible=false,$state=0) {
  // this->x is returned as it was
  // adds lines (y) where DIV bgcolors are filled in
  // allows .00001 as nominal height used for bookmarks/annotations etc.
  if ($collapsible && (sprintf("%0.4f", $this->y)==sprintf("%0.4f", $this->tMargin)) && (!$this->ColActive)) { return; }
  if ($collapsible && (sprintf("%0.4f", $this->y)==sprintf("%0.4f", $this->y0)) && ($this->ColActive) && $this->CurrCol == 0) { return; }	// *COLUMNS*

	// Still use this method if columns or page-break-inside: avoid, as it allows repositioning later
	// otherwise, now uses PaintDivBB()
  if (!$this->ColActive && !$this->keep_block_together && !$this->kwt) {
	if ($move_y && !$this->ColActive) { $this->y += $h; }
	return; 
  }

  if ($level == -3) { $level = $this->blklvl; }
  $firstblockfill = $this->GetFirstBlockFill();
  if ($firstblockfill && $this->blklvl > 0 && $this->blklvl >= $firstblockfill) {
	$last_x = 0;
	$last_w = 0;
	$last_fc = $this->FillColor;
	$bak_x = $this->x;
	$bak_h = $this->divheight;
	$this->divheight = 0;	// Temporarily turn off divheight - as Cell() uses it to check for PageBreak
	for ($blvl=$firstblockfill;$blvl<=$level;$blvl++) {
		$this->SetBlockFill($blvl);
		$this->x = $this->lMargin + $this->blk[$blvl]['outer_left_margin'];
		if ($last_x != $this->lMargin + $this->blk[$blvl]['outer_left_margin'] || $last_w != $this->blk[$blvl]['width'] || $last_fc != $this->FillColor) {
			$x = $this->x;
			$this->Cell( ($this->blk[$blvl]['width']), $h, '', '', 0, '', 1);
			if (!$this->keep_block_together && !$this->writingHTMLheader && !$this->writingHTMLfooter) {
				$this->x = $x;
				// $state = 0 normal; 1 top; 2 bottom; 3 top and bottom
				if ($blvl == $this->blklvl) { $this->PaintDivLnBorder($state,$blvl,$h); }
				else { $this->PaintDivLnBorder(0,$blvl,$h); }
			}
		}
		$last_x = $this->lMargin + $this->blk[$blvl]['outer_left_margin'];
		$last_w = $this->blk[$blvl]['width'];
		$last_fc = $this->FillColor;
	}
	// Reset current block fill
	if (isset($this->blk[$this->blklvl]['bgcolorarray'])) { 
		$bcor = $this->blk[$this->blklvl]['bgcolorarray'];
		$this->SetFColor($bcor);
	}
	$this->x = $bak_x;
	$this->divheight = $bak_h;
  }
  if ($move_y) { $this->y += $h; }
}
/*-- END HTML-CSS --*/


function SetX($x)
{
	//Set x position
	if($x >= 0)	$this->x=$x;
	else $this->x = $this->w + $x;
}

function SetY($y)
{
	//Set y position and reset x
	$this->x=$this->lMargin;
	if($y>=0)
		$this->y=$y;
	else
		$this->y=$this->h+$y;
}

function SetXY($x,$y)
{
	//Set x and y positions
	$this->SetY($y);
	$this->SetX($x);
}


function Output($name='',$dest='')
{
	//Output PDF to some destination
	if ($this->showStats) {
		echo '<div>Generated in '.sprintf('%.2F',(microtime(true) - $this->time0)).' seconds</div>';
	}
	//Finish document if necessary
	if ($this->progressBar) { $this->UpdateProgressBar(1,'100','Finished'); }	// *PROGRESS-BAR*
	if($this->state < 3) $this->Close();
	if ($this->progressBar) { $this->UpdateProgressBar(2,'100','Finished'); }	// *PROGRESS-BAR*
	// fn. error_get_last is only in PHP>=5.2
	if ($this->debug && function_exists('error_get_last') && error_get_last()) {
	   $e = error_get_last(); 
	   if (($e['type'] < 2048 && $e['type'] != 8) || (intval($e['type']) & intval(ini_get("error_reporting")))) {
		echo "<p>Error message detected - PDF file generation aborted.</p>"; 
		echo $e['message'].'<br />';
		echo 'File: '.$e['file'].'<br />';
		echo 'Line: '.$e['line'].'<br />';
		exit; 
	   }
	}


	if (($this->PDFA || $this->PDFX) && $this->encrypted) { $this->Error("PDFA1-b or PDFX/1-a does not permit encryption of documents."); }
	if (count($this->PDFAXwarnings) && (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto))) {
		if ($this->PDFA) {
			echo '<div>WARNING - This file could not be generated as it stands as a PDFA1-b compliant file.</div>';
			echo '<div>These issues can be automatically fixed by mPDF using <i>$mpdf-&gt;PDFAauto=true;</i></div>';
			echo '<div>Action that mPDF will take to automatically force PDFA1-b compliance are shown in brackets.</div>';
		}
		else {
			echo '<div>WARNING - This file could not be generated as it stands as a PDFX/1-a compliant file.</div>';
			echo '<div>These issues can be automatically fixed by mPDF using <i>$mpdf-&gt;PDFXauto=true;</i></div>';
			echo '<div>Action that mPDF will take to automatically force PDFX/1-a compliance are shown in brackets.</div>';
		}
		echo '<div>Warning(s) generated:</div><ul>';
		$this->PDFAXwarnings = array_unique($this->PDFAXwarnings);
		foreach($this->PDFAXwarnings AS $w) {
			echo '<li>'.$w.'</li>';
		}
		echo '</ul>';
		exit;
	}

	if ($this->showStats) {
		echo '<div>Compiled in '.sprintf('%.2F',(microtime(true) - $this->time0)).' seconds (total)</div>';
		echo '<div>Peak Memory usage '.number_format((memory_get_peak_usage(true)/(1024*1024)),2).' MB</div>';
		echo '<div>PDF file size '.number_format((strlen($this->buffer)/1024)).' kB</div>';
		echo '<div>Number of fonts '.count($this->fonts).'</div>';
		exit;
	}


	if(is_bool($dest)) $dest=$dest ? 'D' : 'F';
	$dest=strtoupper($dest);
	if($dest=='') {
		if($name=='') {
			$name='mpdf.pdf';
			$dest='I';
		}
		else { $dest='F'; }
	}

/*-- PROGRESS-BAR --*/
	if ($this->progressBar && ($dest=='D' || $dest=='I')) {
		if($name=='') { $name='mpdf.pdf'; }
		$tempfile = '_tempPDF'.RAND(1,10000);
		//Save to local file
		$f=fopen(_MPDF_TEMP_PATH.$tempfile.'.pdf','wb');
		if(!$f) $this->Error('Unable to create temporary output file: '.$tempfile.'.pdf');
		fwrite($f,$this->buffer,strlen($this->buffer));
		fclose($f);
		$this->UpdateProgressBar(3,'','Finished');

		echo '<script type="text/javascript">

		var form = document.createElement("form");
		form.setAttribute("method", "post");
		form.setAttribute("action", "'._MPDF_URI.'includes/out.php");

		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "filename");
		hiddenField.setAttribute("value", "'.$tempfile.'");
		form.appendChild(hiddenField);

		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "dest");
		hiddenField.setAttribute("value", "'.$dest.'");
		form.appendChild(hiddenField);

		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "opname");
		hiddenField.setAttribute("value", "'.$name.'");
		form.appendChild(hiddenField);

		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "path");
		hiddenField.setAttribute("value", "'.urlencode(_MPDF_TEMP_PATH).'");
		form.appendChild(hiddenField);

		document.body.appendChild(form); 
		form.submit();

      	</script>
		</div>
		</body>
		</html>';
		exit;
	}
	else {
		if ($this->progressBar) { $this->UpdateProgressBar(3,'','Finished'); }
/*-- END PROGRESS-BAR --*/

		switch($dest) {
		   case 'I':
			if ($this->debug && !$this->allow_output_buffering && ob_get_contents()) { echo "<p>Output has already been sent from the script - PDF file generation aborted.</p>"; exit; }
			//Send to standard output
			if(PHP_SAPI!='cli') {
				//We send to a browser
				header('Content-Type: application/pdf');
				if(headers_sent())
					$this->Error('Some data has already been output to browser, can\'t send PDF file');
				if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
					// don't use length if server using compression
					header('Content-Length: '.strlen($this->buffer));
				}
				header('Content-disposition: inline; filename="'.$name.'"');
				header('Cache-Control: public, must-revalidate, max-age=0'); 
				header('Pragma: public');
				header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); 
				header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
			}
			echo $this->buffer;
			break;
		   case 'D':
			//Download file
			header('Content-Description: File Transfer');
			if (headers_sent())
				$this->Error('Some data has already been output to browser, can\'t send PDF file');
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: public, must-revalidate, max-age=0');
			header('Pragma: public');
			header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream', false);
			header('Content-Type: application/download', false);
			header('Content-Type: application/pdf', false);
			if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
				// don't use length if server using compression
				header('Content-Length: '.strlen($this->buffer));
			}
			header('Content-disposition: attachment; filename="'.$name.'"');
 			echo $this->buffer;
			break;
		   case 'F':
			//Save to local file
			$f=fopen($name,'wb');
			if(!$f) $this->Error('Unable to create output file: '.$name);
			fwrite($f,$this->buffer,strlen($this->buffer));
			fclose($f);
			break;
		   case 'S':
			//Return as a string
			return $this->buffer;
		   default:
			$this->Error('Incorrect output destination: '.$dest);
		}

	}	// *PROGRESS-BAR*
	//======================================================================================================
	// mPDF 5.3.79
	// DELETE OLD TMP FILES - Housekeeping
	// Delete any files in tmp/ directory that are >1 hrs old
		$interval = 3600;
		if ($handle = opendir(preg_replace('/\/$/','',_MPDF_TEMP_PATH))) {
		   while (false !== ($file = readdir($handle))) { 
			if (((filemtime(_MPDF_TEMP_PATH.$file)+$interval) < time()) && ($file != "..") && ($file != ".")) { 
				unlink(_MPDF_TEMP_PATH.$file); 
			}
		   }
		   closedir($handle); 
		}
	//==============================================================================================================

	return '';
}


// *****************************************************************************
//                                                                             *
//                             Protected methods                               *
//                                                                             *
// *****************************************************************************
function _dochecks()
{
	//Check for locale-related bug
	if(1.1==1)
		$this->Error('Don\'t alter the locale before including mPDF');
	//Check for decimal separator
	if(sprintf('%.1f',1.0)!='1.0')
		setlocale(LC_NUMERIC,'C');
}

function _begindoc()
{
	//Start document
	$this->state=1;
	$this->_out('%PDF-'.$this->pdf_version);
	$this->_out('%'.chr(226).chr(227).chr(207).chr(211));	// 4 chars > 128 to show binary file
}


/*-- HTMLHEADERS-FOOTERS --*/
function _puthtmlheaders() {
	$this->state=2;
	$nb=$this->page;
	for($n=1;$n<=$nb;$n++) {
	  if ($this->mirrorMargins && $n%2==0) { $OE = 'E'; }	// EVEN
	  else { $OE = 'O'; }
	  $this->page = $n;
	  if (isset($this->saveHTMLHeader[$n][$OE])) {
		$html = $this->saveHTMLHeader[$n][$OE]['html'];
		$this->lMargin = $this->saveHTMLHeader[$n][$OE]['ml'];
		$this->rMargin = $this->saveHTMLHeader[$n][$OE]['mr'];
		$this->tMargin = $this->saveHTMLHeader[$n][$OE]['mh'];
		$this->bMargin = $this->saveHTMLHeader[$n][$OE]['mf'];
		$this->margin_header = $this->saveHTMLHeader[$n][$OE]['mh'];
		$this->margin_footer = $this->saveHTMLHeader[$n][$OE]['mf'];
		$this->w = $this->saveHTMLHeader[$n][$OE]['pw'];
		$this->h = $this->saveHTMLHeader[$n][$OE]['ph'];
		$rotate = (isset($this->saveHTMLHeader[$n][$OE]['rotate']) ? $this->saveHTMLHeader[$n][$OE]['rotate'] : null);
		$this->Reset();
		$this->pageoutput[$n] = array();
		$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
		$this->x = $this->lMargin;
		$this->y = $this->margin_header;

		$html = str_replace('{PAGENO}',$this->pagenumPrefix.$this->docPageNum($n).$this->pagenumSuffix,$html);
		$html = str_replace($this->aliasNbPgGp,$this->nbpgPrefix.$this->docPageNumTotal($n).$this->nbpgSuffix,$html );	// {nbpg}
		$html = str_replace($this->aliasNbPg,$nb,$html );	// {nb}
		$html = preg_replace('/\{DATE\s+(.*?)\}/e',"date('\\1')",$html );

		$this->HTMLheaderPageLinks = array();
		$this->HTMLheaderPageAnnots = array();
		$this->HTMLheaderPageForms = array();
		$this->pageBackgrounds = array();

		$this->writingHTMLheader = true;
		$this->WriteHTML($html , 4);	// parameter 4 saves output to $this->headerbuffer
		$this->writingHTMLheader = false;
		$this->Reset();
		$this->pageoutput[$n] = array();

		$s = $this->PrintPageBackgrounds();
		$this->headerbuffer = $s . $this->headerbuffer;
		$os = '';
		if ($rotate) {
			$os .= sprintf('q 0 -1 1 0 0 %.3F cm ',($this->w*_MPDFK));
		}
		$os .= $this->headerbuffer ;
		if ($rotate) {
			$os .= ' Q' . "\n";
		}

		// Writes over the page background but behind any other output on page
		$os = preg_replace('/\\\\/','\\\\\\\\',$os);
		$this->pages[$n] = preg_replace('/(___HEADER___MARKER'.date('jY').')/', "\n".$os."\n".'\\1', $this->pages[$n]);

		$lks = $this->HTMLheaderPageLinks; 
		foreach($lks AS $lk) {
			if ($rotate) {
				$lw = $lk[2];
				$lh = $lk[3];
				$lk[2] = $lh;
				$lk[3] = $lw;	// swap width and height
				$ax = $lk[0]/_MPDFK;
				$ay = $lk[1]/_MPDFK;
				$bx = $ay-($lh/_MPDFK);
				$by = $this->w-$ax;
				$lk[0] = $bx*_MPDFK;
				$lk[1] = ($this->h-$by)*_MPDFK - $lw;
			}
			$this->PageLinks[$n][]=$lk;
		}
/*-- FORMS --*/
		foreach($this->HTMLheaderPageForms AS $f) {
			$this->form->forms[$f['n']] = $f;
		}
/*-- END FORMS --*/


	  }
	  if (isset($this->saveHTMLFooter[$n][$OE])) {
		$html = $this->saveHTMLFooter[$this->page][$OE]['html'];
		$this->lMargin = $this->saveHTMLFooter[$n][$OE]['ml'];
		$this->rMargin = $this->saveHTMLFooter[$n][$OE]['mr'];
		$this->tMargin = $this->saveHTMLFooter[$n][$OE]['mh'];
		$this->bMargin = $this->saveHTMLFooter[$n][$OE]['mf'];
		$this->margin_header = $this->saveHTMLFooter[$n][$OE]['mh'];
		$this->margin_footer = $this->saveHTMLFooter[$n][$OE]['mf'];
		$this->w = $this->saveHTMLFooter[$n][$OE]['pw'];
		$this->h = $this->saveHTMLFooter[$n][$OE]['ph'];
		$rotate = (isset($this->saveHTMLFooter[$n][$OE]['rotate']) ? $this->saveHTMLFooter[$n][$OE]['rotate'] : null);
		$this->Reset();
		$this->pageoutput[$n] = array();
		$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
		$this->x = $this->lMargin;
		$top_y = $this->y = $this->h - $this->margin_footer;

		// if bottom-margin==0, corrects to avoid division by zero
		if ($this->y == $this->h) { $top_y = $this->y = ($this->h - 0.1); }

		$html = str_replace('{PAGENO}',$this->pagenumPrefix.$this->docPageNum($n).$this->pagenumSuffix,$html);
		$html = str_replace($this->aliasNbPgGp,$this->nbpgPrefix.$this->docPageNumTotal($n).$this->nbpgSuffix,$html );	// {nbpg}
		$html = str_replace($this->aliasNbPg,$nb,$html );	// {nb}
		$html = preg_replace('/\{DATE\s+(.*?)\}/e',"date('\\1')",$html );


		$this->HTMLheaderPageLinks = array();
		$this->HTMLheaderPageAnnots = array();
		$this->HTMLheaderPageForms = array();
		$this->pageBackgrounds = array();

		$this->writingHTMLfooter = true;
		$this->InFooter = true;
		$this->WriteHTML($html , 4);	// parameter 4 saves output to $this->headerbuffer
		$this->writingHTMLfooter = false;
		$this->InFooter = false;
		$this->Reset();
		$this->pageoutput[$n] = array();

		$fheight = $this->y - $top_y;
		$adj = -$fheight;

		$s = $this->PrintPageBackgrounds(-$adj);
		$this->headerbuffer = $s . $this->headerbuffer;

		$os = '';
		$os .= $this->StartTransform(true)."\n";
		if ($rotate) {
			$os .= sprintf('q 0 -1 1 0 0 %.3F cm ',($this->w*_MPDFK));
		}
		$os .= $this->transformTranslate(0, $adj, true)."\n";
		$os .= $this->headerbuffer ;
		if ($rotate) {
			$os .= ' Q' . "\n";
		}
		$os .= $this->StopTransform(true)."\n";
		// Writes over the page background but behind any other output on page
		$os = preg_replace('/\\\\/','\\\\\\\\',$os);
		$this->pages[$n] = preg_replace('/(___HEADER___MARKER'.date('jY').')/', "\n".$os."\n".'\\1', $this->pages[$n]);

		$lks = $this->HTMLheaderPageLinks; 
		foreach($lks AS $lk) {
			$lk[1] -= $adj*_MPDFK;
			if ($rotate) {
				$lw = $lk[2];
				$lh = $lk[3];
				$lk[2] = $lh;
				$lk[3] = $lw;	// swap width and height

				$ax = $lk[0]/_MPDFK;
				$ay = $lk[1]/_MPDFK;
				$bx = $ay-($lh/_MPDFK);
				$by = $this->w-$ax;
				$lk[0] = $bx*_MPDFK;
				$lk[1] = ($this->h-$by)*_MPDFK - $lw;
			}
			$this->PageLinks[$n][]=$lk;
		}
/*-- FORMS --*/
		foreach($this->HTMLheaderPageForms AS $f) {
			$f['y'] += $adj;
			$this->form->forms[$f['n']] = $f;
		}
/*-- END FORMS --*/
	  }
	}
	$this->page=$nb;
	$this->state=1;
}
/*-- END HTMLHEADERS-FOOTERS --*/


function _putpages()
{
	$nb=$this->page;
	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';

	if($this->DefOrientation=='P') {
		$defwPt=$this->fwPt;
		$defhPt=$this->fhPt;
	}
	else {
		$defwPt=$this->fhPt;
		$defhPt=$this->fwPt;
	}
	$annotid=(3+2*$nb);

	// Active Forms
	$totaladdnum = 0;
	for($n=1;$n<=$nb;$n++) {
		if (isset($this->PageLinks[$n])) { $totaladdnum += count($this->PageLinks[$n]); }
/*-- ANNOTATIONS --*/
		if (isset($this->PageAnnots[$n])) { 
			foreach ($this->PageAnnots[$n] as $k => $pl) {
				if (!empty($pl['opt']['popup']) || !empty($pl['opt']['file'])) { $totaladdnum += 2 ; }
				else { $totaladdnum++; }
			}
		}
/*-- END ANNOTATIONS --*/

/*-- FORMS --*/
		if ( count($this->form->forms) > 0 ) {
			$this->form->countPageForms($n, $totaladdnum);
		}
/*-- END FORMS --*/
	}
/*-- FORMS --*/
	// Make a note in the radio button group of the obj_id it will have
	$ctr = 0;
	if (count($this->form->form_radio_groups)) {
		foreach($this->form->form_radio_groups AS $name=>$frg) {
			$this->form->form_radio_groups[$name]['obj_id'] = $annotid + $totaladdnum + $ctr;
			$ctr++;
		}
	}
/*-- END FORMS --*/

	// mPDF 5.3.99
	// Select unused fonts (usually default font)
	$unused = array();
	foreach($this->fonts as $fk=>$font) {
	   if (!$font['used'] && ($font['type']=='TTF')) { 
		$unused[] = $fk;
	   }
	}


	for($n=1;$n<=$nb;$n++)
	{
		$thispage = $this->pages[$n];	// mPDF 5.3.99
		unset($this->pages[$n]);	// mPDF 5.3.99
		if(isset($this->OrientationChanges[$n])) { 
			$hPt=$this->pageDim[$n]['w']*_MPDFK;
			$wPt=$this->pageDim[$n]['h']*_MPDFK;
			$owidthPt_LR = $this->pageDim[$n]['outer_width_TB']*_MPDFK;
			$owidthPt_TB = $this->pageDim[$n]['outer_width_LR']*_MPDFK;
		}
		else { 
			$wPt=$this->pageDim[$n]['w']*_MPDFK;
			$hPt=$this->pageDim[$n]['h']*_MPDFK;
			$owidthPt_LR = $this->pageDim[$n]['outer_width_LR']*_MPDFK;
			$owidthPt_TB = $this->pageDim[$n]['outer_width_TB']*_MPDFK;
		}
		// mPDF 5.3.99
		// Remove references to unused fonts (usually default font)
		foreach($unused as $fk) {
			if ($this->fonts[$fk]['sip'] || $this->fonts[$fk]['smp']) {
				foreach($this->fonts[$fk]['subsetfontids'] AS $k => $fid) {
						$thispage = preg_replace('/\s\/F'.$fid.' \d[\d.]* Tf\s/is',' ',$thispage); 
				}
			}
			else { 
				$thispage = preg_replace('/\s\/F'.$this->fonts[$fk]['i'].' \d[\d.]* Tf\s/is',' ',$thispage); 
			}
		}
		//Replace number of pages
		if(!empty($this->aliasNbPg)) {
			if (!$this->onlyCoreFonts) { $s1 = $this->UTF8ToUTF16BE($this->aliasNbPg, false); }	// mPDF 5.3.22
			$s2 = $this->aliasNbPg;
			if (!$this->onlyCoreFonts) { $r1 = $this->UTF8ToUTF16BE($nb, false); }	// mPDF 5.3.22
			$r2 = $nb;
			if (preg_match_all('/{mpdfheadernbpg (C|R) ff=(\S*) fs=(\S*) fz=(.*?)}/',$thispage,$m)) {
				for($hi=0;$hi<count($m[0]);$hi++) {
					$pos = $m[1][$hi];
					$hff = $m[2][$hi];
					$hfst = $m[3][$hi];
					$hfsz = $m[4][$hi];
					$this->SetFont($hff,$hfst,$hfsz, false);
					$x1 = $this->GetStringWidth($this->aliasNbPg);
					$x2 = $this->GetStringWidth($nb);
					$xadj = $x1 - $x2;
					if ($pos=='C') { $xadj /= 2; }
					$rep = sprintf(' q 1 0 0 1 %.3F 0 cm ', $xadj*_MPDFK); 
					$thispage = str_replace($m[0][$hi], $rep, $thispage);
				}
			}
			if (!$this->onlyCoreFonts) { $thispage=str_replace($s1,$r1,$thispage); }	// mPDF 5.3.22
			$thispage=str_replace($s2,$r2,$thispage);	// mPDF 5.3.22

			// And now for any SMP/SIP fonts subset using <HH> format
			$r = '';
			$nstr = "$nb";
			for($i=0;$i<strlen($nstr);$i++) {
				$r .= sprintf("%02s", strtoupper(dechex(intval($nstr[$i])+48))); 
			}
			$thispage=str_replace($this->aliasNbPgHex,$r,$thispage);

		}
		//Replace number of pages in group
		if(!empty($this->aliasNbPgGp)) {
			if (!$this->onlyCoreFonts) { $s1 = $this->UTF8ToUTF16BE($this->aliasNbPgGp, false); }	// mPDF 5.3.22
			$s2 = $this->aliasNbPgGp; 
			$nbt = $this->docPageNumTotal($n);
			if (!$this->onlyCoreFonts) { $r1 = $this->UTF8ToUTF16BE($nbt, false); }	// mPDF 5.3.22
			$r2 = $nbt;
			if (preg_match_all('/{mpdfheadernbpggp (C|R) ff=(\S*) fs=(\S*) fz=(.*?)}/',$thispage,$m)) {
				for($hi=0;$hi<count($m[0]);$hi++) {
					$pos = $m[1][$hi];
					$hff = $m[2][$hi];
					$hfst = $m[3][$hi];
					$hfsz = $m[4][$hi];
					$this->SetFont($hff,$hfst,$hfsz, false);
					$x1 = $this->GetStringWidth($this->aliasNbPgGp);
					$x2 = $this->GetStringWidth($nbt);
					$xadj = $x1 - $x2;
					if ($pos=='C') { $xadj /= 2; }
					$rep = sprintf(' q 1 0 0 1 %.3F 0 cm ', $xadj*_MPDFK); 
					$thispage = str_replace($m[0][$hi], $rep, $thispage);
				}
			}
			if (!$this->onlyCoreFonts) { $thispage=str_replace($s1,$r1,$thispage); }	// mPDF 5.3.22
			$thispage=str_replace($s2,$r2,$thispage);

			// And now for any SMP/SIP fonts subset using <HH> format
			$r = '';
			$nstr = "$nbt";
			for($i=0;$i<strlen($nstr);$i++) {
				$r .= sprintf("%02s", strtoupper(dechex(intval($nstr[$i])+48))); 
			}
			$thispage=str_replace($this->aliasNbPgGpHex,$r,$thispage);

		}
		$thispage = preg_replace('/(\s*___BACKGROUND___PATTERNS'.date('jY').'\s*)/', " ", $thispage);
		$thispage = preg_replace('/(\s*___HEADER___MARKER'.date('jY').'\s*)/', " ", $thispage);
		$thispage = preg_replace('/(\s*___PAGE___START'.date('jY').'\s*)/', " ", $thispage);
		$thispage = preg_replace('/(\s*___TABLE___BACKGROUNDS'.date('jY').'\s*)/', " ", $thispage);

		//Page
		$this->_newobj();
		$this->_out('<</Type /Page');
		$this->_out('/Parent 1 0 R');
		if(isset($this->OrientationChanges[$n])) {
			$this->_out(sprintf('/MediaBox [0 0 %.3F %.3F]',$hPt,$wPt));
			//If BleedBox is defined, it must be larger than the TrimBox, but smaller than the MediaBox
			$bleedMargin = $this->pageDim[$n]['bleedMargin']*_MPDFK;
			if ($bleedMargin && ($owidthPt_TB || $owidthPt_LR)) {
				$x0 = $owidthPt_TB-$bleedMargin;
				$y0 = $owidthPt_LR-$bleedMargin;
				$x1 = $hPt-$owidthPt_TB+$bleedMargin;
				$y1 = $wPt-$owidthPt_LR+$bleedMargin;
				$this->_out(sprintf('/BleedBox [%.3F %.3F %.3F %.3F]', $x0, $y0, $x1, $y1));
			}
			$this->_out(sprintf('/TrimBox [%.3F %.3F %.3F %.3F]', $owidthPt_TB, $owidthPt_LR, ($hPt-$owidthPt_TB), ($wPt-$owidthPt_LR)));	
			if (isset($this->OrientationChanges[$n]) && $this->displayDefaultOrientation) {
				if ($this->DefOrientation=='P') { $this->_out('/Rotate 270'); }
				else { $this->_out('/Rotate 90'); }
			}
		}
		//else if($wPt != $defwPt || $hPt != $defhPt) {
		else {
			$this->_out(sprintf('/MediaBox [0 0 %.3F %.3F]',$wPt,$hPt));
			$bleedMargin = $this->pageDim[$n]['bleedMargin']*_MPDFK;
			if ($bleedMargin && ($owidthPt_TB || $owidthPt_LR)) {
				$x0 = $owidthPt_LR-$bleedMargin;
				$y0 = $owidthPt_TB-$bleedMargin;
				$x1 = $wPt-$owidthPt_LR+$bleedMargin;
				$y1 = $hPt-$owidthPt_TB+$bleedMargin;
				$this->_out(sprintf('/BleedBox [%.3F %.3F %.3F %.3F]', $x0, $y0, $x1, $y1));
			}
			$this->_out(sprintf('/TrimBox [%.3F %.3F %.3F %.3F]', $owidthPt_LR, $owidthPt_TB, ($wPt-$owidthPt_LR), ($hPt-$owidthPt_TB)));	
		}
		$this->_out('/Resources 2 0 R');

		// Important to keep in RGB colorSpace when using transparency
		if (!$this->PDFA && !$this->PDFX) { 
			if ($this->restrictColorSpace == 3)
				$this->_out('/Group << /Type /Group /S /Transparency /CS /DeviceCMYK >> ');
			else if ($this->restrictColorSpace == 1)
				$this->_out('/Group << /Type /Group /S /Transparency /CS /DeviceGray >> ');
			else 
				$this->_out('/Group << /Type /Group /S /Transparency /CS /DeviceRGB >> ');
		}

		$annotsnum = 0;
		if (isset($this->PageLinks[$n])) { $annotsnum += count($this->PageLinks[$n]); }
/*-- ANNOTATIONS --*/
		if (isset($this->PageAnnots[$n])) { 
			foreach ($this->PageAnnots[$n] as $k => $pl) {
				if (!empty($pl['opt']['popup']) || !empty($pl['opt']['file'])) { $annotsnum += 2 ; }
				else { $annotsnum++; }
				$this->PageAnnots[$n][$k]['pageobj'] = $this->n;
			}
		}
/*-- END ANNOTATIONS --*/

/*-- FORMS --*/
		// Active Forms
		$formsnum = 0;
		if ( count($this->form->forms) > 0 ) {
			foreach( $this->form->forms as $val ) {
				if ( $val['page'] == $n )
					$formsnum++;
			}
		}
/*-- END FORMS --*/
		if ($annotsnum || $formsnum) {
			$s = '/Annots [ ';
			for($i=0;$i<$annotsnum;$i++) { 
				$s .= ($annotid + $i) . ' 0 R ';
			} 
			$annotid += $annotsnum;
/*-- FORMS --*/
			if ( count($this->form->forms) > 0 ) {
				$this->form->addFormIds($n, $s, $annotid);
			}
/*-- END FORMS --*/
			$s .= '] ';
			$this->_out($s);
		}

		$this->_out('/Contents '.($this->n+1).' 0 R>>');
		$this->_out('endobj');

		//Page content
		$this->_newobj();
		$p=($this->compress) ? gzcompress($thispage) : $thispage;
		$this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
		$this->_putstream($p);
		$this->_out('endobj');
	}
	$this->_putannots($n);

	//Pages root
	$this->offsets[1]=strlen($this->buffer);
	$this->_out('1 0 obj');
	$this->_out('<</Type /Pages');
	$kids='/Kids [';
	for($i=0;$i<$nb;$i++)
		$kids.=(3+2*$i).' 0 R ';
	$this->_out($kids.']');
	$this->_out('/Count '.$nb);
	$this->_out(sprintf('/MediaBox [0 0 %.3F %.3F]',$defwPt,$defhPt));
	$this->_out('>>');
	$this->_out('endobj');
}


function _putannots($n) {
	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	$nb=$this->page;
	for($n=1;$n<=$nb;$n++)
	{
		$annotobjs = array();
		if(isset($this->PageLinks[$n]) || isset($this->PageAnnots[$n]) || count($this->form->forms) > 0 ) {
			$wPt=$this->pageDim[$n]['w']*_MPDFK;
			$hPt=$this->pageDim[$n]['h']*_MPDFK;

			//Links
			if(isset($this->PageLinks[$n])) {
			   foreach($this->PageLinks[$n] as $key => $pl) {
				$this->_newobj();
				$annot='';
				$rect=sprintf('%.3F %.3F %.3F %.3F',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
				$annot .= '<</Type /Annot /Subtype /Link /Rect ['.$rect.']';
				$annot .= ' /Contents '.$this->_UTF16BEtextstring($pl[4]);
				$annot .= ' /NM '.$this->_textstring(sprintf('%04u-%04u', $n, $key));
				$annot .= ' /M '.$this->_textstring('D:'.date('YmdHis'));
				$annot .= ' /Border [0 0 0]';
				// mPDF 5.3.16  5.3.52 Use this (instead of /Border) to specify border around link
		//		$annot .= ' /BS <</W 1';	// Width on points; 0 = no line
		//		$annot .= ' /S /D';		// style - [S]olid, [D]ashed, [B]eveled, [I]nset, [U]nderline
		//		$annot .= ' /D [3 2]';		// Dash array - if dashed
		//		$annot .= ' >>';
		//		$annot .= ' /C [1 0 0]';	// Color RGB

				if ($this->PDFA || $this->PDFX) { $annot .= ' /F 28'; }
				if (strpos($pl[4],'@')===0) {
					$p=substr($pl[4],1);
					//	$h=isset($this->OrientationChanges[$p]) ? $wPt : $hPt;
					$htarg=$this->pageDim[$p]['h']*_MPDFK;
					$annot.=sprintf(' /Dest [%d 0 R /XYZ 0 %.3F null]>>',1+2*$p,$htarg);
				}
				else if(is_string($pl[4])) {
					$annot .= ' /A <</S /URI /URI '.$this->_textstring($pl[4]).'>> >>';
				}
				else {
					$l=$this->links[$pl[4]];
					// may not be set if #link points to non-existent target
					if (isset($this->pageDim[$l[0]]['h'])) { $htarg=$this->pageDim[$l[0]]['h']*_MPDFK; }
					else { $htarg=$this->h*_MPDFK; } // doesn't really matter
					$annot.=sprintf(' /Dest [%d 0 R /XYZ 0 %.3F null]>>',1+2*$l[0],$htarg-$l[1]*_MPDFK);
				}
				$this->_out($annot);
				$this->_out('endobj');
			   }
			}


/*-- ANNOTATIONS --*/
			if(isset($this->PageAnnots[$n])) {
			   foreach ($this->PageAnnots[$n] as $key => $pl) {
				if ($pl['opt']['file']) { $FileAttachment=true; }
				else { $FileAttachment=false; }
				$this->_newobj();
				$annot='';
				$pl['opt'] = array_change_key_case($pl['opt'], CASE_LOWER);
				$x = $pl['x']; 
				if ($this->annotMargin <> 0 || $x==0 || $x<0) {	// Odd page
				   $x = ($wPt/_MPDFK) - $this->annotMargin;
				}
				$w = $h = 0;
				$a = $x * _MPDFK;
				$b = $hPt - ($pl['y']  * _MPDFK);
				$annot .= '<</Type /Annot ';
				if ($FileAttachment) { 
					$annot .= '/Subtype /FileAttachment'; 
					// Need to set a size for FileAttachment icons
					if ($pl['opt']['icon']=='Paperclip') { $w=8.235; $h=20; }	// 7,17
					else if ($pl['opt']['icon']=='Tag') { $w=20; $h=16; }
					else if ($pl['opt']['icon']=='Graph') { $w=20; $h=20; }
					else { $w=14; $h=20; } 	// PushPin 
					$f = $pl['opt']['file'];
					$f = preg_replace('/^.*\//', '', $f);
					$f = preg_replace('/[^a-zA-Z0-9._]/', '', $f);
					$annot .= '/FS <</Type /Filespec /F ('.$f.')';
					$annot .= '/EF <</F '.($this->n+1).' 0 R>>';
					$annot .= '>>';
				}
				else { 
					$annot .= '/Subtype /Text'; 
				}
				$rect = sprintf('%.3F %.3F %.3F %.3F', $a, $b-$h, $a+$w, $b);
				$annot .= '/Rect ['.$rect.']';

				// contents = description of file in free text
				$annot .= ' /Contents '.$this->_UTF16BEtextstring($pl['txt']);
				$annot .= ' /NM '.$this->_textstring(sprintf('%04u-%04u', $n, (2000 + $key)));
				$annot .= ' /M '.$this->_textstring('D:'.date('YmdHis'));
				$annot .= ' /CreationDate '.$this->_textstring('D:'.date('YmdHis'));
				$annot .= ' /Border [0 0 0]';
				if ($this->PDFA || $this->PDFX) { 
					$annot .= ' /F 28'; 
					$annot .= ' /CA 1'; 
				}
				else if ($pl['opt']['ca']>0) { $annot .= ' /CA '.$pl['opt']['ca']; }

				$annotcolor = ' /C [';
				// mPDF 5.3.74
				if (isset($pl['opt']['c']) AND $pl['opt']['c']) {
					$col = $pl['opt']['c'];
					if ($col{0}==3 || $col{0}==5) { $annotcolor .= sprintf("%.3F %.3F %.3F", ord($col{1})/255,ord($col{2})/255,ord($col{3})/255); }
					else if ($col{0}==1) { $annotcolor .= sprintf("%.3F", ord($col{1})/255); }
					else if ($col{0}==4 || $col{0}==6) { $annotcolor .= sprintf("%.3F %.3F %.3F %.3F", ord($col{1})/100,ord($col{2})/100,ord($col{3})/100,ord($col{4})/100); }
					else { $annotcolor .= '1 1 0'; }
				}
				else { $annotcolor .= '1 1 0'; }
				$annotcolor .= ']';
				$annot .= $annotcolor;
				// Usually Author
				// Use as Title for fileattachment
				if (isset($pl['opt']['t']) AND is_string($pl['opt']['t'])) {
					$annot .= ' /T '.$this->_UTF16BEtextstring($pl['opt']['t']);
				}
				if ($FileAttachment) {
					$iconsapp = array('Paperclip', 'Graph', 'PushPin', 'Tag'); 
				}
				else { $iconsapp = array('Comment', 'Help', 'Insert', 'Key', 'NewParagraph', 'Note', 'Paragraph'); }
				if (isset($pl['opt']['icon']) AND in_array($pl['opt']['icon'], $iconsapp)) {
					$annot .= ' /Name /'.$pl['opt']['icon'];
				}
				else if ($FileAttachment) { $annot .= ' /Name /PushPin'; }
				else { $annot .= ' /Name /Note'; }
				if (!$FileAttachment) {
					// /Subj is PDF 1.5 spec.
					if (isset($pl['opt']['subj']) && !$this->PDFA && !$this->PDFX) {
						$annot .= ' /Subj '.$this->_UTF16BEtextstring($pl['opt']['subj']);
					}
					if (!empty($pl['opt']['popup'])) { 
						$annot .= ' /Open true'; 
						$annot .= ' /Popup '.($this->n+1).' 0 R';
					}
					else { $annot .= ' /Open false'; }
				}
				$annot .= ' /P '.$pl['pageobj'].' 0 R';
				$annot .= '>>';
				$this->_out($annot);
				$this->_out('endobj');

				if ($FileAttachment) { 
					$file = @file_get_contents($pl['opt']['file']) or die('mPDF Error: Cannot access file attachment - '.$pl['opt']['file']);
					$filestream = gzcompress($file);
					$this->_newobj();
					$this->_out('<</Type /EmbeddedFile');
					$this->_out('/Length '.strlen($filestream));
					$this->_out('/Filter /FlateDecode');
					$this->_out('>>');
					$this->_putstream($filestream);
					$this->_out('endobj');
				}
				else if (!empty($pl['opt']['popup'])) { 
					$this->_newobj();
					$annot='';
					if (is_array($pl['opt']['popup']) && isset($pl['opt']['popup'][0])) { $x = $pl['opt']['popup'][0] * _MPDFK; }
					else { $x = $pl['x'] * _MPDFK; }
					if (is_array($pl['opt']['popup']) && isset($pl['opt']['popup'][1])) { $y = $hPt - ($pl['opt']['popup'][1] * _MPDFK); }
					else { $y = $hPt - ($pl['y']  * _MPDFK); }
					if (is_array($pl['opt']['popup']) && isset($pl['opt']['popup'][2])) { $w = $pl['opt']['popup'][2] * _MPDFK; }
					else { $w = 180; }
					if (is_array($pl['opt']['popup']) && isset($pl['opt']['popup'][3])) { $h = $pl['opt']['popup'][3] * _MPDFK; }
					else { $h = 120; }
					$rect = sprintf('%.3F %.3F %.3F %.3F', $x, $y-$h, $x+$w, $y);
					$annot .= '<</Type /Annot /Subtype /Popup /Rect ['.$rect.']';
					$annot .= ' /M '.$this->_textstring('D:'.date('YmdHis'));
					if ($this->PDFA || $this->PDFX) { $annot .= ' /F 28'; }
					$annot .= ' /Parent '.($this->n-1).' 0 R';
					$annot .= '>>';
					$this->_out($annot);
					$this->_out('endobj');
				}
			   }
			}
/*-- END ANNOTATIONS --*/

/*-- FORMS --*/
			// Active Forms
			if ( count($this->form->forms) > 0 ) {
				$this->form->_putFormItems($n, $hPt);
			}
/*-- END FORMS --*/
		}
	}
/*-- FORMS --*/
	// Active Forms - Radio Button Group entries
	// Output Radio Button Group form entries (radio_on_obj_id already determined)
	if (count($this->form->form_radio_groups)) {
		$this->form->_putRadioItems($n);
	}
/*-- END FORMS --*/
}


/*-- ANNOTATIONS --*/
function Annotation($text, $x=0, $y=0, $icon='Note', $author='', $subject='', $opacity=0, $colarray=false, $popup='', $file='') {
	// mPDF 5.3.94
	if (is_array($colarray) && count($colarray)==3) { $colarray = $this->ConvertColor('rgb('.$colarray[0].','.$colarray[1].','.$colarray[2].')'); }
	if ($colarray === false) { $colarray = $this->ConvertColor('yellow'); }
	if ($x==0) { $x = $this->x; }
	if ($y==0) { $y = $this->y; }
	$page = $this->page;
	if ($page < 1) {	// Document has not been started - assume it's for first page
		$page = 1;
		if ($x==0) { $x = $this->lMargin; }
		if ($y==0) { $y = $this->tMargin; }
	}

	if ($this->PDFA || $this->PDFX) {
		if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) { $this->PDFAXwarnings[] = "Annotation markers cannot be semi-transparent in PDFA1-b or PDFX/1-a, so they may make underlying text unreadable. (Annotation markers moved to right margin)"; }
		$x = ($this->w) - $this->rMargin*0.66;
	}
	if (!$this->annotMargin) { $y -= $this->FontSize / 2; }

	if (!$opacity && $this->annotMargin) { $opacity = 1; }
	else if (!$opacity) { $opacity = $this->annotOpacity; }

	$an = array('txt' => $text, 'x' => $x, 'y' => $y, 'opt' => array('Icon'=>$icon, 'T'=>$author, 'Subj'=>$subject, 'C'=>$colarray, 'CA'=>$opacity, 'popup'=>$popup, 'file'=>$file));

	if ($this->keep_block_together) {	// Save to array - don't write yet
		$this->ktAnnots[$this->page][]= $an;
		return;
	}
	else if ($this->table_rotate) {
		$this->tbrot_Annots[$this->page][]= $an;
		return;
	}
	else if ($this->kwt) {
		$this->kwt_Annots[$this->page][]= $an;
		return;
	}
	// mPDF 5.0 
	if ($this->writingHTMLheader || $this->writingHTMLfooter) {
		$this->HTMLheaderPageAnnots[]= $an;
		return;
	}
	//Put an Annotation on the page
	$this->PageAnnots[$page][] = $an;
/*-- COLUMNS --*/
	// Save cross-reference to Column buffer
	$ref = count($this->PageAnnots[$this->page])-1;
	$this->columnAnnots[$this->CurrCol][INTVAL($this->x)][INTVAL($this->y)] = $ref;
/*-- END COLUMNS --*/
}
/*-- END ANNOTATIONS --*/


function _putfonts() {
	$nf=$this->n;
	$mqr=$this->_getMQR();
	if ($mqr) { set_magic_quotes_runtime(0); }
	foreach($this->FontFiles as $fontkey=>$info) {
	   // TrueType embedded
	   if (isset($info['type']) && $info['type']=='TTF' && !$info['sip'] && !$info['smp']) {
		$used = true;
		$asSubset = false;
		foreach($this->fonts AS $k=>$f) {
			if ($f['fontkey'] == $fontkey && $f['type']=='TTF') { 
				$used = $f['used']; 
				if ($used) {
					$nChars = (ord($f['cw'][0]) << 8) + ord($f['cw'][1]);
					$usage = intval(count($f['subset'])*100 / $nChars);
					$fsize = $info['length1'];
					// Always subset the very large TTF files
					if ($fsize > ($this->maxTTFFilesize *1024)) { $asSubset = true; }
					else if ($usage < $this->percentSubset) { $asSubset = true; }
				}
				if ($this->PDFA || $this->PDFX)  $asSubset = false; 	// mPDF 5.3.45
				$this->fonts[$k]['asSubset'] = $asSubset;
				break;
			}
		}
		if ($used && !$asSubset) {
			//Font file embedding
			$this->_newobj();
			$this->FontFiles[$fontkey]['n']=$this->n;
			$font='';
			$originalsize = $info['length1'];
			if ($this->repackageTTF || $this->fonts[$fontkey]['TTCfontID']>0) {
				// First see if there is a cached compressed file
				if (file_exists(_MPDF_TTFONTDATAPATH.$fontkey.'.ps.z')) {
					$f=fopen(_MPDF_TTFONTDATAPATH.$fontkey.'.ps.z','rb');
					if(!$f) { $this->Error('Font file .ps.z not found'); }
					while(!feof($f)) { $font .= fread($f, 2048); }
					fclose($f);
					include(_MPDF_TTFONTDATAPATH.$fontkey.'.ps.php');	// sets $originalsize (of repackaged font)
				}
				else {
					if (!class_exists('TTFontFile', false)) { include(_MPDF_PATH .'classes/ttfontsuni.php'); }
					$ttf = new TTFontFile();
					$font = $ttf->repackageTTF($this->FontFiles[$fontkey]['ttffile'], $this->fonts[$fontkey]['TTCfontID'], $this->debugfonts);

					$originalsize = strlen($font);
					$font = gzcompress($font);
					unset($ttf);
					if (is_writable(dirname(_MPDF_TTFONTDATAPATH.'x'))) {
						$fh = fopen(_MPDF_TTFONTDATAPATH.$fontkey.'.ps.z',"wb");
						fwrite($fh,$font,strlen($font));
						fclose($fh);
						$fh = fopen(_MPDF_TTFONTDATAPATH.$fontkey.'.ps.php',"wb");
						$len = "<?php	 	 \n";
						$len.='$originalsize='.$originalsize.";\n";
						$len.="?>";
						fwrite($fh,$len,strlen($len));
						fclose($fh);
					}
				}
			}
			else {
				// First see if there is a cached compressed file
				if (file_exists(_MPDF_TTFONTDATAPATH.$fontkey.'.z')) {
					$f=fopen(_MPDF_TTFONTDATAPATH.$fontkey.'.z','rb');
					if(!$f) { $this->Error('Font file not found'); }
					while(!feof($f)) { $font .= fread($f, 2048); }
					fclose($f);
				}
				else {
					$f=fopen($this->FontFiles[$fontkey]['ttffile'],'rb');
					if(!$f) { $this->Error('Font file not found'); }
					while(!feof($f)) { $font .= fread($f, 2048); }
					fclose($f);
					$font = gzcompress($font);
					if (is_writable(dirname(_MPDF_TTFONTDATAPATH.'x'))) {
						$fh = fopen(_MPDF_TTFONTDATAPATH.$fontkey.'.z',"wb");
						fwrite($fh,$font,strlen($font));
						fclose($fh);
					}
				}
			}

			$this->_out('<</Length '.strlen($font));
			$this->_out('/Filter /FlateDecode');
			$this->_out('/Length1 '.$originalsize);
			$this->_out('>>');
			$this->_putstream($font);
			$this->_out('endobj');
		}
	   }
	}

	$nfonts = count($this->fonts);
	$fctr = 1;
	foreach($this->fonts as $k=>$font) {
		//Font objects
		$type=$font['type'];
		$name=$font['name'];
		if ((!isset($font['used']) || !$font['used']) && $type=='TTF') { continue; }
		if ($this->progressBar) { $this->UpdateProgressBar(2,intval($fctr*100/$nfonts),'Writing Fonts'); $fctr++; }	// *PROGRESS-BAR*
		if (isset($font['asSubset'])) { $asSubset = $font['asSubset']; }
		else { $asSubset = ''; }
/*-- CJK-FONTS --*/
		if($type=='Type0') { 	// = Adobe CJK Fonts
			$this->fonts[$k]['n']=$this->n+1;
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_putType0($font);
		}
		else
/*-- END CJK-FONTS --*/
		if($type=='core') {
			//Standard font
			$this->fonts[$k]['n']=$this->n+1;
			if ($this->PDFA || $this->PDFX) { $this->Error('Core fonts are not allowed in PDF/A1-b or PDFX/1-a files (Times, Helvetica, Courier etc.)'); }
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/BaseFont /'.$name);
			$this->_out('/Subtype /Type1');
			if($name!='Symbol' && $name!='ZapfDingbats') {
				$this->_out('/Encoding /WinAnsiEncoding');
			}
			$this->_out('>>');
			$this->_out('endobj');
		} 
		// TrueType embedded SUBSETS for SIP (CJK extB containing Supplementary Ideographic Plane 2)
		// Or Unicode Plane 1 - Supplementary Multilingual Plane
		else if ($type=='TTF' && ($font['sip'] || $font['smp'])) {
		   if (!$font['used']) { continue; }
		   $ssfaid="AA";
		   if (!class_exists('TTFontFile', false)) { include(_MPDF_PATH .'classes/ttfontsuni.php'); }
		   $ttf = new TTFontFile();
		   for($sfid=0;$sfid<count($font['subsetfontids']);$sfid++) {
			$this->fonts[$k]['n'][$sfid]=$this->n+1;		// NB an array for subset
			$subsetname = 'MPDF'.$ssfaid.'+'.$font['name'];
			$ssfaid++;
			$subset = $font['subsets'][$sfid];
			unset($subset[0]);
			$ttfontstream = $ttf->makeSubsetSIP($font['ttffile'], $subset, $font['TTCfontID'], $this->debugfonts);
			$ttfontsize = strlen($ttfontstream);
			$fontstream = gzcompress($ttfontstream);
			$widthstring = '';
			$toUnistring = '';
			foreach($font['subsets'][$sfid] AS $cp=>$u) {
				$w = $this->_getCharWidth($font['cw'], $u); 
				if ($w !== false) {
					$widthstring .= $w.' ';
				}
				else {
					$widthstring .= round($ttf->defaultWidth).' ';
				}
				if ($u > 65535) {
					$utf8 = chr(($u>>18)+240).chr((($u>>12)&63)+128).chr((($u>>6)&63)+128) .chr(($u&63)+128);
					$utf16 = mb_convert_encoding($utf8, 'UTF-16BE', 'UTF-8');
					$l1 = ord($utf16[0]);
					$h1 = ord($utf16[1]);
					$l2 = ord($utf16[2]);
					$h2 = ord($utf16[3]);
					$toUnistring .= sprintf("<%02s> <%02s%02s%02s%02s>\n", strtoupper(dechex($cp)), strtoupper(dechex($l1)), strtoupper(dechex($h1)), strtoupper(dechex($l2)), strtoupper(dechex($h2)));
				}
				else {
					$toUnistring .= sprintf("<%02s> <%04s>\n", strtoupper(dechex($cp)), strtoupper(dechex($u)));
				}
			}

			//Additional Type1 or TrueType font
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/BaseFont /'.$subsetname);
			$this->_out('/Subtype /TrueType');
			$this->_out('/FirstChar 0 /LastChar '.(count($font['subsets'][$sfid])-1));
			$this->_out('/Widths '.($this->n+1).' 0 R');
			$this->_out('/FontDescriptor '.($this->n+2).' 0 R');
			$this->_out('/ToUnicode '.($this->n + 3).' 0 R');
			$this->_out('>>');
			$this->_out('endobj');

			//Widths
			$this->_newobj();
			$this->_out('['.$widthstring.']');
			$this->_out('endobj');

			//Descriptor
			$this->_newobj();
			$s='<</Type /FontDescriptor /FontName /'.$subsetname."\n";
			foreach($font['desc'] as $kd=>$v) {
				if ($kd == 'Flags') { $v = $v | 4; $v = $v & ~32; }	// SYMBOLIC font flag
				$s.=' /'.$kd.' '.$v."\n";
			}
			$s.='/FontFile2 '.($this->n + 2).' 0 R';
			$this->_out($s.'>>');
			$this->_out('endobj');

			// ToUnicode
			// mPDF 5.3.35
			$this->_newobj();
			$toUni = "/CIDInit /ProcSet findresource begin\n";
			$toUni .= "12 dict begin\n";
			$toUni .= "begincmap\n";
			$toUni .= "/CIDSystemInfo\n";
			$toUni .= "<</Registry (Adobe)\n";
			$toUni .= "/Ordering (UCS)\n";
			$toUni .= "/Supplement 0\n";
			$toUni .= ">> def\n";
			$toUni .= "/CMapName /Adobe-Identity-UCS def\n";
			$toUni .= "/CMapType 2 def\n";
			$toUni .= "1 begincodespacerange\n";
			$toUni .= "<00> <FF>\n";
			$toUni .= "endcodespacerange\n";
			$toUni .= count($font['subsets'][$sfid])." beginbfchar\n";
			$toUni .= $toUnistring;
			$toUni .= "endbfchar\n";
			$toUni .= "endcmap\n";
			$toUni .= "CMapName currentdict /CMap defineresource pop\n";
			$toUni .= "end\n";
			$toUni .= "end\n";

			$this->_out('<</Length '.(strlen($toUni)).'>>');
			$this->_putstream($toUni);
			$this->_out('endobj');

			//Font file 
			$this->_newobj();
			$this->_out('<</Length '.strlen($fontstream));
			$this->_out('/Filter /FlateDecode');
			$this->_out('/Length1 '.$ttfontsize);
			$this->_out('>>');
			$this->_putstream($fontstream);
			$this->_out('endobj');
		   }	// foreach subset
		   unset($ttf);
		} 
		// TrueType embedded SUBSETS or FULL
		else if ($type=='TTF') {
			$this->fonts[$k]['n']=$this->n+1;
			if ($asSubset ) {
				$ssfaid="A";
				if (!class_exists('TTFontFile', false)) { include(_MPDF_PATH .'classes/ttfontsuni.php'); }
				$ttf = new TTFontFile();
				$fontname = 'MPDFA'.$ssfaid.'+'.$font['name'];
				$subset = $font['subset'];
				unset($subset[0]);
				$ttfontstream = $ttf->makeSubset($font['ttffile'], $subset, $font['TTCfontID'], $this->debugfonts);
				$ttfontsize = strlen($ttfontstream);
				$fontstream = gzcompress($ttfontstream);
				$codeToGlyph = $ttf->codeToGlyph;
				unset($codeToGlyph[0]);
			}
			else { $fontname = $font['name']; }
			// Type0 Font
			// A composite font - a font composed of other fonts, organized hierarchically
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/Subtype /Type0');
			$this->_out('/BaseFont /'.$fontname.'');
			$this->_out('/Encoding /Identity-H'); 
			$this->_out('/DescendantFonts ['.($this->n + 1).' 0 R]');
			$this->_out('/ToUnicode '.($this->n + 2).' 0 R');
			$this->_out('>>');
			$this->_out('endobj');

			// CIDFontType2
			// A CIDFont whose glyph descriptions are based on TrueType font technology
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/Subtype /CIDFontType2');
			$this->_out('/BaseFont /'.$fontname.'');
			$this->_out('/CIDSystemInfo '.($this->n + 2).' 0 R'); 
			$this->_out('/FontDescriptor '.($this->n + 3).' 0 R');
			if (isset($font['desc']['MissingWidth'])){
				$this->_out('/DW '.$font['desc']['MissingWidth'].''); 
			}

			if (!$asSubset && file_exists(_MPDF_TTFONTDATAPATH.$font['fontkey'].'.cw')) {
					$w = '';
					$w=file_get_contents(_MPDF_TTFONTDATAPATH.$font['fontkey'].'.cw');
					$this->_out($w);
			}
			else {
				$this->_putTTfontwidths($font, $asSubset, $ttf->maxUni);
			}

			$this->_out('/CIDToGIDMap '.($this->n + 4).' 0 R');
			$this->_out('>>');
			$this->_out('endobj');

			// ToUnicode
			// mPDF 5.3.35
			$this->_newobj();
			$toUni = "/CIDInit /ProcSet findresource begin\n";
			$toUni .= "12 dict begin\n";
			$toUni .= "begincmap\n";
			$toUni .= "/CIDSystemInfo\n";
			$toUni .= "<</Registry (Adobe)\n";
			$toUni .= "/Ordering (UCS)\n";
			$toUni .= "/Supplement 0\n";
			$toUni .= ">> def\n";
			$toUni .= "/CMapName /Adobe-Identity-UCS def\n";
			$toUni .= "/CMapType 2 def\n";
			$toUni .= "1 begincodespacerange\n";
			$toUni .= "<0000> <FFFF>\n";
			$toUni .= "endcodespacerange\n";
			$toUni .= "1 beginbfrange\n";
			$toUni .= "<0000> <FFFF> <0000>\n";
			$toUni .= "endbfrange\n";
			$toUni .= "endcmap\n";
			$toUni .= "CMapName currentdict /CMap defineresource pop\n";
			$toUni .= "end\n";
			$toUni .= "end\n";
			$this->_out('<</Length '.(strlen($toUni)).'>>');
			$this->_putstream($toUni);
			$this->_out('endobj');


			// CIDSystemInfo dictionary
			$this->_newobj();
			$this->_out('<</Registry (Adobe)'); 
			$this->_out('/Ordering (UCS)');
			$this->_out('/Supplement 0');
			$this->_out('>>');
			$this->_out('endobj');

			// Font descriptor
			$this->_newobj();
			$this->_out('<</Type /FontDescriptor');
			$this->_out('/FontName /'.$fontname);
			foreach($font['desc'] as $kd=>$v) {
				if ($asSubset && $kd == 'Flags') { $v = $v | 4; $v = $v & ~32; }	// SYMBOLIC font flag
				$this->_out(' /'.$kd.' '.$v);
			}
			if ($font['panose']) {
				$this->_out(' /Style << /Panose <'.$font['panose'].'> >>');
			}
			if ($asSubset ) {
				$this->_out('/FontFile2 '.($this->n + 2).' 0 R');
			}
			else if ($font['fontkey']) {
				// obj ID of a stream containing a TrueType font program
				$this->_out('/FontFile2 '.$this->FontFiles[$font['fontkey']]['n'].' 0 R');
			}
			$this->_out('>>');
			$this->_out('endobj');

			// Embed CIDToGIDMap
			// A specification of the mapping from CIDs to glyph indices
			if ($asSubset ) {
				$cidtogidmap = '';
				$cidtogidmap = str_pad('', 256*256*2, "\x00");
				foreach($codeToGlyph as $cc=>$glyph) {
					$cidtogidmap[$cc*2] = chr($glyph >> 8);
					$cidtogidmap[$cc*2 + 1] = chr($glyph & 0xFF);
				}
				$cidtogidmap = gzcompress($cidtogidmap);
			}
			else {
				// First see if there is a cached CIDToGIDMapfile
				$cidtogidmap = '';
				if (file_exists(_MPDF_TTFONTDATAPATH.$font['fontkey'].'.cgm')) {
					$f=fopen(_MPDF_TTFONTDATAPATH.$font['fontkey'].'.cgm','rb');
					while(!feof($f)) { $cidtogidmap .= fread($f, 2048); }
					fclose($f);
				}
				else {
					if (!class_exists('TTFontFile', false)) { include(_MPDF_PATH .'classes/ttfontsuni.php'); }
					$ttf = new TTFontFile();
					$charToGlyph = $ttf->getCTG($font['ttffile'], $font['TTCfontID'], $this->debugfonts);
					$cidtogidmap = str_pad('', 256*256*2, "\x00");
					foreach($charToGlyph as $cc=>$glyph) {
						$cidtogidmap[$cc*2] = chr($glyph >> 8);
						$cidtogidmap[$cc*2 + 1] = chr($glyph & 0xFF);
					}
					unset($ttf);
					$cidtogidmap = gzcompress($cidtogidmap);
					if (is_writable(dirname(_MPDF_TTFONTDATAPATH.'x'))) {
						$fh = fopen(_MPDF_TTFONTDATAPATH.$font['fontkey'].'.cgm',"wb");
						fwrite($fh,$cidtogidmap,strlen($cidtogidmap));
						fclose($fh);
					}
				}
			}
			$this->_newobj();
			$this->_out('<</Length '.strlen($cidtogidmap).'');
			$this->_out('/Filter /FlateDecode');
			$this->_out('>>');
			$this->_putstream($cidtogidmap);
			$this->_out('endobj');

			//Font file 
			if ($asSubset ) {
				$this->_newobj();
				$this->_out('<</Length '.strlen($fontstream));
				$this->_out('/Filter /FlateDecode');
				$this->_out('/Length1 '.$ttfontsize);
				$this->_out('>>');
				$this->_putstream($fontstream);
				$this->_out('endobj');
				unset($ttf);
			}
		} 
		else { $this->Error('Unsupported font type: '.$type.' ('.$name.')'); }
	}
	if ($mqr) { set_magic_quotes_runtime($mqr); }
}



function _putTTfontwidths(&$font, $asSubset, $maxUni) {
	if ($asSubset && file_exists(_MPDF_TTFONTDATAPATH.$font['fontkey'].'.cw127.php')) {
		include(_MPDF_TTFONTDATAPATH.$font['fontkey'].'.cw127.php') ;
		$startcid = 128;
	}
	else {
		$rangeid = 0;
		$range = array();
		$prevcid = -2;
		$prevwidth = -1;
		$interval = false;
		$startcid = 1;
	}
	if ($asSubset) { $cwlen = $maxUni + 1; }
	else { $cwlen = (strlen($font['cw'])/2); }

	// for each character
	for ($cid=$startcid; $cid<$cwlen; $cid++) {
		if ($cid==128 && $asSubset && (!file_exists(_MPDF_TTFONTDATAPATH.$font['fontkey'].'.cw127.php'))) {
			if (is_writable(dirname(_MPDF_TTFONTDATAPATH.'x'))) {
				$fh = fopen(_MPDF_TTFONTDATAPATH.$font['fontkey'].'.cw127.php',"wb");
				$cw127='<?php	 	'."\n";
				$cw127.='$rangeid='.$rangeid.";\n";
				$cw127.='$prevcid='.$prevcid.";\n";
				$cw127.='$prevwidth='.$prevwidth.";\n";
				if ($interval) { $cw127.='$interval=true'.";\n"; }
				else { $cw127.='$interval=false'.";\n"; }
				$cw127.='$range='.var_export($range,true).";\n";
				$cw127.="?>";
				fwrite($fh,$cw127,strlen($cw127));
				fclose($fh);
			}
		}
		if ($font['cw'][$cid*2] == "\00" && $font['cw'][$cid*2+1] == "\00") { continue; }
		$width = (ord($font['cw'][$cid*2]) << 8) + ord($font['cw'][$cid*2+1]);
		if ($width == 65535) { $width = 0; }
		if ($asSubset && $cid > 255 && (!isset($font['subset'][$cid]) || !$font['subset'][$cid])) {
			continue;
		}
		if (!isset($font['dw']) || (isset($font['dw']) && $width != $font['dw'])) {
			if ($cid == ($prevcid + 1)) {
				// consecutive CID
				if ($width == $prevwidth) {
					if ($width == $range[$rangeid][0]) {
						$range[$rangeid][] = $width;
					} else {
						array_pop($range[$rangeid]);
						// new range
						$rangeid = $prevcid;
						$range[$rangeid] = array();
						$range[$rangeid][] = $prevwidth;
						$range[$rangeid][] = $width;
					}
					$interval = true;
					$range[$rangeid]['interval'] = true;
				} else {
					if ($interval) {
						// new range
						$rangeid = $cid;
						$range[$rangeid] = array();
						$range[$rangeid][] = $width;
					} else {
						$range[$rangeid][] = $width;
					}
					$interval = false;
				}
			} else {
				// new range
				$rangeid = $cid;
				$range[$rangeid] = array();
				$range[$rangeid][] = $width;
				$interval = false;
			}
			$prevcid = $cid;
			$prevwidth = $width;
		}
	}
	$w = $this->_putfontranges($range);
	$this->_out($w);
	if (!$asSubset) {
		if (is_writable(dirname(_MPDF_TTFONTDATAPATH.'x'))) {
			$fh = fopen(_MPDF_TTFONTDATAPATH.$font['fontkey'].'.cw',"wb");
			fwrite($fh,$w,strlen($w));
			fclose($fh);
		}
	}
}

function _putfontranges(&$range) {
	// optimize ranges
	$prevk = -1;
	$nextk = -1;
	$prevint = false;
	foreach ($range as $k => $ws) {
		$cws = count($ws);
		if (($k == $nextk) AND (!$prevint) AND ((!isset($ws['interval'])) OR ($cws < 4))) {
			if (isset($range[$k]['interval'])) {
				unset($range[$k]['interval']);
			}
			$range[$prevk] = array_merge($range[$prevk], $range[$k]);
			unset($range[$k]);
		} else {
			$prevk = $k;
		}
		$nextk = $k + $cws;
		if (isset($ws['interval'])) {
			if ($cws > 3) {
				$prevint = true;
			} else {
				$prevint = false;
			}
			unset($range[$k]['interval']);
			--$nextk;
		} else {
			$prevint = false;
		}
	}
	// output data
	$w = '';
	foreach ($range as $k => $ws) {
		if (count(array_count_values($ws)) == 1) {
			// interval mode is more compact
			$w .= ' '.$k.' '.($k + count($ws) - 1).' '.$ws[0];
		} else {
			// range mode
			$w .= ' '.$k.' [ '.implode(' ', $ws).' ]' . "\n";
		}
	}
	return '/W ['.$w.' ]';
}


function _putfontwidths(&$font, $cidoffset=0) {
	ksort($font['cw']);
	unset($font['cw'][65535]);
	$rangeid = 0;
	$range = array();
	$prevcid = -2;
	$prevwidth = -1;
	$interval = false;
	// for each character
	foreach ($font['cw'] as $cid => $width) {
		$cid -= $cidoffset;
		if (!isset($font['dw']) || (isset($font['dw']) && $width != $font['dw'])) {
			if ($cid == ($prevcid + 1)) {
				// consecutive CID
				if ($width == $prevwidth) {
					if ($width == $range[$rangeid][0]) {
						$range[$rangeid][] = $width;
					} else {
						array_pop($range[$rangeid]);
						// new range
						$rangeid = $prevcid;
						$range[$rangeid] = array();
						$range[$rangeid][] = $prevwidth;
						$range[$rangeid][] = $width;
					}
					$interval = true;
					$range[$rangeid]['interval'] = true;
				} else {
					if ($interval) {
						// new range
						$rangeid = $cid;
						$range[$rangeid] = array();
						$range[$rangeid][] = $width;
					} else {
						$range[$rangeid][] = $width;
					}
					$interval = false;
				}
			} else {
				// new range
				$rangeid = $cid;
				$range[$rangeid] = array();
				$range[$rangeid][] = $width;
				$interval = false;
			}
			$prevcid = $cid;
			$prevwidth = $width;
		}
	}
	$this->_out($this->_putfontranges($range));
}


/*-- CJK-FONTS --*/

// from class PDF_Chinese CJK EXTENSIONS
function _putType0(&$font)
{
	//Type0
	$this->_out('/Subtype /Type0');
	$this->_out('/BaseFont /'.$font['name'].'-'.$font['CMap']);
	$this->_out('/Encoding /'.$font['CMap']);
	$this->_out('/DescendantFonts ['.($this->n+1).' 0 R]');
	$this->_out('>>');
	$this->_out('endobj');
	//CIDFont
	$this->_newobj();
	$this->_out('<</Type /Font');
	$this->_out('/Subtype /CIDFontType0');
	$this->_out('/BaseFont /'.$font['name']);

	$cidinfo = '/Registry '.$this->_textstring('Adobe');
	$cidinfo .= ' /Ordering '.$this->_textstring($font['registry']['ordering']);
	$cidinfo .= ' /Supplement '.$font['registry']['supplement'];
	$this->_out('/CIDSystemInfo <<'.$cidinfo.'>>');

	$this->_out('/FontDescriptor '.($this->n+1).' 0 R');
	if (isset($font['MissingWidth'])){
		$this->_out('/DW '.$font['MissingWidth'].''); 
	}
	$this->_putfontwidths($font, 31);
	$this->_out('>>');
	$this->_out('endobj');

	//Font descriptor
	$this->_newobj();
	$s = '<</Type /FontDescriptor /FontName /'.$font['name'];
	foreach ($font['desc'] as $k => $v) {
		if ($k != 'Style') {
			$s .= ' /'.$k.' '.$v.'';
		}
	}
	$this->_out($s.'>>');
	$this->_out('endobj');
}
/*-- END CJK-FONTS --*/



function _putimages()
{
	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	reset($this->images);
	while(list($file,$info)=each($this->images)) {
		$this->_newobj();
		$this->images[$file]['n']=$this->n;
		$this->_out('<</Type /XObject');
		$this->_out('/Subtype /Image');
		$this->_out('/Width '.$info['w']);
		$this->_out('/Height '.$info['h']);
		if (isset($info['masked'])) {
			$this->_out('/SMask '.($this->n - 1).' 0 R');
		}
		if($info['cs']=='Indexed') {
			if ($this->PDFX || ($this->PDFA && $this->restrictColorSpace==3)) { $this->Error("PDFA1-b and PDFX/1-a files do not permit using mixed colour space (".$file.")."); }
			$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
		}
		else {
			$this->_out('/ColorSpace /'.$info['cs']);
			if($info['cs']=='DeviceCMYK') {
				if ($this->PDFA && $this->restrictColorSpace!=3) { $this->Error("PDFA1-b does not permit Images using mixed colour space (".$file.")."); }
				if($info['type']=='jpg') { $this->_out('/Decode [1 0 1 0 1 0 1 0]'); }
			}
			else if ($info['cs']=='DeviceRGB' && ($this->PDFX || ($this->PDFA && $this->restrictColorSpace==3))) { $this->Error("PDFA1-b and PDFX/1-a files do not permit using mixed colour space (".$file.")."); }
		}
		$this->_out('/BitsPerComponent '.$info['bpc']);
		if (isset($info['f']) && $info['f']) { $this->_out('/Filter /'.$info['f']); }
		if(isset($info['parms'])) { $this->_out($info['parms']); }
		if(isset($info['trns']) and is_array($info['trns'])) {
			$trns='';
			for($i=0;$i<count($info['trns']);$i++)
				$trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
			$this->_out('/Mask ['.$trns.']');
		}
		$this->_out('/Length '.strlen($info['data']).'>>');
		$this->_putstream($info['data']);

		unset($this->images[$file]['data']);
		$this->_out('endobj');
		//Palette
		if($info['cs']=='Indexed') {
			$this->_newobj();
			$pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
			$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
			$this->_putstream($pal);
			$this->_out('endobj');
		}
	}
}

function _putinfo()
{
	$this->_out('/Producer '.$this->_UTF16BEtextstring('mPDF '.mPDF_VERSION));
	if(!empty($this->title))
		$this->_out('/Title '.$this->_UTF16BEtextstring($this->title));
	if(!empty($this->subject))
		$this->_out('/Subject '.$this->_UTF16BEtextstring($this->subject));
	if(!empty($this->author))
		$this->_out('/Author '.$this->_UTF16BEtextstring($this->author));
	if(!empty($this->keywords))
		$this->_out('/Keywords '.$this->_UTF16BEtextstring($this->keywords));
	if(!empty($this->creator))
		$this->_out('/Creator '.$this->_UTF16BEtextstring($this->creator));

	$z = date('O'); // +0200
	$offset = substr($z,0,3)."'".substr($z,3,2)."'";
	$this->_out('/CreationDate '.$this->_textstring(date('YmdHis').$offset));
	$this->_out('/ModDate '.$this->_textstring(date('YmdHis').$offset));
	if ($this->PDFX) {
		$this->_out('/Trapped/False');
		$this->_out('/GTS_PDFXVersion(PDF/X-1a:2003)');
	}
}

function _putmetadata() {
	$this->_newobj();
	$this->MetadataRoot = $this->n;
	$Producer = 'mPDF '.mPDF_VERSION;
	$z = date('O'); // +0200
	$offset = substr($z,0,3).':'.substr($z,3,2);
	$CreationDate = date('Y-m-d\TH:i:s').$offset;	// 2006-03-10T10:47:26-05:00 2006-06-19T09:05:17Z
	$uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)  );


	$m = '<?xpacket begin="'.chr(239).chr(187).chr(191).'" id="W5M0MpCehiHzreSzNTczkc9d"?>'."\n";	// begin = FEFF BOM
	$m .= ' <x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="3.1-701">'."\n";
	$m .= '  <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">'."\n";
	$m .= '   <rdf:Description rdf:about="uuid:'.$uuid.'" xmlns:pdf="http://ns.adobe.com/pdf/1.3/">'."\n";
	$m .= '    <pdf:Producer>'.$Producer.'</pdf:Producer>'."\n";
	if(!empty($this->keywords)) { $m .= '    <pdf:Keywords>'.$this->keywords.'</pdf:Keywords>'."\n"; }
	$m .= '   </rdf:Description>'."\n";

	$m .= '   <rdf:Description rdf:about="uuid:'.$uuid.'" xmlns:xmp="http://ns.adobe.com/xap/1.0/">'."\n";
	$m .= '    <xmp:CreateDate>'.$CreationDate.'</xmp:CreateDate>'."\n";
	$m .= '    <xmp:ModifyDate>'.$CreationDate.'</xmp:ModifyDate>'."\n";
	$m .= '    <xmp:MetadataDate>'.$CreationDate.'</xmp:MetadataDate>'."\n";
	if(!empty($this->creator)) { $m .= '    <xmp:CreatorTool>'.$this->creator.'</xmp:CreatorTool>'."\n"; }
	$m .= '   </rdf:Description>'."\n";

	// DC elements
	$m .= '   <rdf:Description rdf:about="uuid:'.$uuid.'" xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n";
	$m .= '    <dc:format>application/pdf</dc:format>'."\n";
	if(!empty($this->title)) {
		$m .= '    <dc:title>
     <rdf:Alt>
      <rdf:li xml:lang="x-default">'.$this->title.'</rdf:li>
     </rdf:Alt>
    </dc:title>'."\n";
	}
	if(!empty($this->keywords)) {
		$m .= '    <dc:subject>
     <rdf:Bag>
      <rdf:li>'.$this->keywords.'</rdf:li>
     </rdf:Bag>
    </dc:subject>'."\n";
	}
	if(!empty($this->subject)) {
		$m .= '    <dc:description>
     <rdf:Alt>
      <rdf:li xml:lang="x-default">'.$this->subject.'</rdf:li>
     </rdf:Alt>
    </dc:description>'."\n";
	}
	if(!empty($this->author)) {
		$m .= '    <dc:creator>
     <rdf:Seq>
      <rdf:li>'.$this->author.'</rdf:li>
     </rdf:Seq>
    </dc:creator>'."\n";
	}
	$m .= '   </rdf:Description>'."\n";


	// This bit is specific to PDFX-1a
	if ($this->PDFX) {
		$m .= '   <rdf:Description rdf:about="uuid:'.$uuid.'" xmlns:pdfx="http://ns.adobe.com/pdfx/1.3/" pdfx:Apag_PDFX_Checkup="1.3" pdfx:GTS_PDFXConformance="PDF/X-1a:2003" pdfx:GTS_PDFXVersion="PDF/X-1:2003"/>'."\n";
	}

	// This bit is specific to PDFA-1b
	else if ($this->PDFA) {
		$m .= '   <rdf:Description rdf:about="uuid:'.$uuid.'" xmlns:pdfaid="http://www.aiim.org/pdfa/ns/id/" >'."\n";
		$m .= '    <pdfaid:part>1</pdfaid:part>'."\n";
		$m .= '    <pdfaid:conformance>B</pdfaid:conformance>'."\n";
		$m .= '    <pdfaid:amd>2005</pdfaid:amd>'."\n";
		$m .= '   </rdf:Description>'."\n";
	}

	$m .= '   <rdf:Description rdf:about="uuid:'.$uuid.'" xmlns:xmpMM="http://ns.adobe.com/xap/1.0/mm/">'."\n";
	$m .= '    <xmpMM:DocumentID>uuid:'.$uuid.'</xmpMM:DocumentID>'."\n";
	$m .= '   </rdf:Description>'."\n";
	$m .= '  </rdf:RDF>'."\n";
	$m .= ' </x:xmpmeta>'."\n";
	$m .= str_repeat(str_repeat(' ',100)."\n",20);	// 2-4kB whitespace padding required
	$m .= '<?xpacket end="w"?>';	// "r" read only
	$this->_out('<</Type/Metadata/Subtype/XML/Length '.strlen($m).'>>');
	$this->_putstream($m);
	$this->_out('endobj');
}

function _putoutputintent() {
	$this->_newobj();
	$this->OutputIntentRoot = $this->n;
	$this->_out('<</Type /OutputIntent');

	if ($this->PDFA) {
		$this->_out('/S /GTS_PDFA1');
		if ($this->ICCProfile) {
			$this->_out('/Info ('.preg_replace('/_/',' ',$this->ICCProfile).')');
			$this->_out('/OutputConditionIdentifier (Custom)');
			$this->_out('/OutputCondition ()');
		}
		else {
			$this->_out('/Info (sRGB IEC61966-2.1)');
			$this->_out('/OutputConditionIdentifier (sRGB IEC61966-2.1)');
			$this->_out('/OutputCondition ()');
		}
		$this->_out('/DestOutputProfile '.($this->n+1).' 0 R');
	}
	else if ($this->PDFX) {	// always a CMYK profile
		$this->_out('/S /GTS_PDFX');
		if ($this->ICCProfile) {
			$this->_out('/Info ('.preg_replace('/_/',' ',$this->ICCProfile).')');
			$this->_out('/OutputConditionIdentifier (Custom)');
			$this->_out('/OutputCondition ()');
			$this->_out('/DestOutputProfile '.($this->n+1).' 0 R');
		}
		else {
			$this->_out('/Info (CGATS TR 001)');
			$this->_out('/OutputConditionIdentifier (CGATS TR 001)');
			$this->_out('/OutputCondition (CGATS TR 001 (SWOP))');
			$this->_out('/RegistryName (http://www.color.org)');
		}
	}
	$this->_out('>>');
	$this->_out('endobj');

	if ($this->PDFX && !$this->ICCProfile) { return; } // no ICCProfile embedded

	$this->_newobj();
	if ($this->ICCProfile)
		$s = file_get_contents(_MPDF_PATH.'iccprofiles/'.$this->ICCProfile.'.icc');
	else 
		$s = file_get_contents(_MPDF_PATH.'iccprofiles/sRGB_IEC61966-2-1.icc');
	if ($this->compress) { $s = gzcompress($s); }
	$this->_out('<<');
	if ($this->PDFX || ($this->PDFA && $this->restrictColorSpace == 3)) { $this->_out('/N 4'); }
	else { $this->_out('/N 3'); }
	if ($this->compress)
		$this->_out('/Filter /FlateDecode ');
	$this->_out('/Length '.strlen($s).'>>');
	$this->_putstream($s);
	$this->_out('endobj');
}


function _putcatalog() {
	$this->_out('/Type /Catalog');
	$this->_out('/Pages 1 0 R');
	if($this->ZoomMode=='fullpage')	$this->_out('/OpenAction [3 0 R /Fit]');
	elseif($this->ZoomMode=='fullwidth') $this->_out('/OpenAction [3 0 R /FitH null]');
	elseif($this->ZoomMode=='real')	$this->_out('/OpenAction [3 0 R /XYZ null null 1]');
	elseif(!is_string($this->ZoomMode))	$this->_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode/100).']');
	else	$this->_out('/OpenAction [3 0 R /XYZ null null null]');
	if($this->LayoutMode=='single')	$this->_out('/PageLayout /SinglePage');
	elseif($this->LayoutMode=='continuous')	$this->_out('/PageLayout /OneColumn');
	elseif($this->LayoutMode=='twoleft')	$this->_out('/PageLayout /TwoColumnLeft');
	elseif($this->LayoutMode=='tworight')	$this->_out('/PageLayout /TwoColumnRight');
	elseif($this->LayoutMode=='two') {
	  if ($this->mirrorMargins) { $this->_out('/PageLayout /TwoColumnRight'); }
	  else { $this->_out('/PageLayout /TwoColumnLeft'); }
	}

/*-- BOOKMARKS --*/
	if(count($this->BMoutlines)>0) {
	      $this->_out('/Outlines '.$this->OutlineRoot.' 0 R');
	      $this->_out('/PageMode /UseOutlines');
	}
/*-- END BOOKMARKS --*/
	if(is_int(strpos($this->DisplayPreferences,'FullScreen'))) $this->_out('/PageMode /FullScreen');

	// Metadata
	if ($this->PDFA || $this->PDFX) { 
		$this->_out('/Metadata '.$this->MetadataRoot.' 0 R');
	}
	// OutputIntents
	if ($this->PDFA || $this->PDFX || $this->ICCProfile) { 
		$this->_out('/OutputIntents ['.$this->OutputIntentRoot.' 0 R]');
	}

/*-- FORMS --*/
	if (count($this->form->forms)>0) {
		$this->form->_putFormsCatalog();
	}
/*-- END FORMS --*/
	if ( isset($this->js) ) {
		$this->_out('/Names << /JavaScript '.($this->n_js).' 0 R >> ');
	}

	if($this->DisplayPreferences || $this->directionality == 'rtl' || $this->mirrorMargins) {
		$this->_out('/ViewerPreferences<<');
		if(is_int(strpos($this->DisplayPreferences,'HideMenubar'))) $this->_out('/HideMenubar true');
		if(is_int(strpos($this->DisplayPreferences,'HideToolbar'))) $this->_out('/HideToolbar true');
		if(is_int(strpos($this->DisplayPreferences,'HideWindowUI'))) $this->_out('/HideWindowUI true');
		if(is_int(strpos($this->DisplayPreferences,'DisplayDocTitle'))) $this->_out('/DisplayDocTitle true');
		if(is_int(strpos($this->DisplayPreferences,'CenterWindow'))) $this->_out('/CenterWindow true');
		if(is_int(strpos($this->DisplayPreferences,'FitWindow'))) $this->_out('/FitWindow true');
		// /PrintScaling is PDF 1.6 spec.
		if(is_int(strpos($this->DisplayPreferences,'NoPrintScaling')) && !$this->PDFA && !$this->PDFX) 
			$this->_out('/PrintScaling /None');
		if($this->directionality == 'rtl') $this->_out('/Direction /R2L');
		// /Duplex is PDF 1.7 spec.
		if($this->mirrorMargins && !$this->PDFA && !$this->PDFX) {
			// if ($this->DefOrientation=='P') $this->_out('/Duplex /DuplexFlipShortEdge');
			$this->_out('/Duplex /DuplexFlipLongEdge');	// PDF v1.7+
		}
		$this->_out('>>');
	}
	if ($this->hasOC) {	// mPDF 5.3.45
		// mPDF 5.3.41  Visibility
		$p=$this->n_ocg_print.' 0 R';
		$v=$this->n_ocg_view.' 0 R';
		$h=$this->n_ocg_hidden.' 0 R';
		$as="<</Event /Print /OCGs [$p $v $h] /Category [/Print]>> <</Event /View /OCGs [$p $v $h] /Category [/View]>>";
		$this->_out("/OCProperties <</OCGs [$p $v $h] /D <</ON [$p] /OFF [$v] /OFF [$h] /AS [$as]>>>>");
	}
}

// Inactive function left for backwards compatability
function SetUserRights($enable=true, $annots="", $form="", $signature="") {
	// Does nothing
}

function _enddoc() {
	if ($this->progressBar) { $this->UpdateProgressBar(2,'10','Writing Headers & Footers'); }	// *PROGRESS-BAR*
	$this->_puthtmlheaders();	// *HTMLHEADERS-FOOTERS*
	if ($this->progressBar) { $this->UpdateProgressBar(2,'20','Writing Pages'); }	// *PROGRESS-BAR*
	// Remove references to unused fonts (usually default font)
	foreach($this->fonts as $fk=>$font) {
	   if (!$font['used'] && ($font['type']=='TTF')) { 
		if ($font['sip'] || $font['smp']) {
			foreach($font['subsetfontids'] AS $k => $fid) {
				foreach($this->pages AS $pn=>$page) { 
					$this->pages[$pn] = preg_replace('/\s\/F'.$fid.' \d[\d.]* Tf\s/is',' ',$this->pages[$pn]); 
				}
			}
		}
		else { 
				foreach($this->pages AS $pn=>$page) { 
					$this->pages[$pn] = preg_replace('/\s\/F'.$font['i'].' \d[\d.]* Tf\s/is',' ',$this->pages[$pn]); 
				}
		}
	   }
	}

	$this->_putpages();
	if ($this->progressBar) { $this->UpdateProgressBar(2,'30','Writing document resources'); }	// *PROGRESS-BAR*

	$this->_putresources();
	//Info
	$this->_newobj();
	$this->InfoRoot = $this->n;
	$this->_out('<<');
	if ($this->progressBar) { $this->UpdateProgressBar(2,'80','Writing document info'); }	// *PROGRESS-BAR*
	$this->_putinfo();
	$this->_out('>>');
	$this->_out('endobj');

	// METADATA
	if ($this->PDFA || $this->PDFX) { $this->_putmetadata(); }
	// OUTPUTINTENT
	if ($this->PDFA || $this->PDFX || $this->ICCProfile) { $this->_putoutputintent(); }

	//Catalog
	$this->_newobj();
	$this->_out('<<');
	if ($this->progressBar) { $this->UpdateProgressBar(2,'90','Writing document catalog'); }	// *PROGRESS-BAR*
	$this->_putcatalog();
	$this->_out('>>');
	$this->_out('endobj');
	//Cross-ref
	$o=strlen($this->buffer);
	$this->_out('xref');
	$this->_out('0 '.($this->n+1));
	$this->_out('0000000000 65535 f ');
	for($i=1; $i <= $this->n ; $i++)
		$this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
	//Trailer
	$this->_out('trailer');
	$this->_out('<<');
	$this->_puttrailer();
	$this->_out('>>');
	$this->_out('startxref');
	$this->_out($o);

	$this->buffer .= '%%EOF';
	$this->state=3;
/*-- IMPORTS --*/

	if ($this->enableImports && count($this->parsers) > 0) {
	  	foreach ($this->parsers as $k => $_){
			$this->parsers[$k]->closeFile();
			$this->parsers[$k] = null;
			unset($this->parsers[$k]);
		}
	}
/*-- END IMPORTS --*/
}

function _beginpage($orientation,$mgl='',$mgr='',$mgt='',$mgb='',$mgh='',$mgf='',$ohname='',$ehname='',$ofname='',$efname='',$ohvalue=0,$ehvalue=0,$ofvalue=0,$efvalue=0,$pagesel='',$newformat='') {
	if (!($pagesel && $this->page==1 && (sprintf("%0.4f", $this->y)==sprintf("%0.4f", $this->tMargin)))) { 
		$this->page++;
		$this->pages[$this->page]='';
	}
	$this->state=2;
	$resetHTMLHeadersrequired = false;


	if ($newformat) { $this->_setPageSize($newformat, $orientation); }
/*-- CSS-PAGE --*/
	// Paged media (page-box)

	if ($pagesel || (isset($this->page_box['using']) && $this->page_box['using'])) {
		if ($pagesel || $this->page==1) { $first = true; }
		else { $first = false; }
		if ($this->mirrorMargins && ($this->page % 2==0)) { $oddEven = 'E'; }
		else { $oddEven = 'O'; }
		if ($pagesel) { $psel = $pagesel; }
		else if ($this->page_box['current']) { $psel = $this->page_box['current']; }
		else { $psel = ''; }
		list($orientation,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$hname,$fname,$bg,$resetpagenum,$pagenumstyle,$suppress,$marks,$newformat) = $this->SetPagedMediaCSS($psel, $first, $oddEven);
		if ($this->mirrorMargins && ($this->page % 2==0)) { 
			if ($hname) { $ehvalue = 1; $ehname = $hname; } else { $ehvalue = -1; }
			if ($fname) { $efvalue = 1; $efname = $fname; } else { $efvalue = -1; }
		}
		else { 
			if ($hname) { $ohvalue = 1; $ohname = $hname; } else { $ohvalue = -1; }
			if ($fname) { $ofvalue = 1; $ofname = $fname; } else { $ofvalue = -1; }
		}
		if ($resetpagenum || $pagenumstyle || $suppress) {
			$this->PageNumSubstitutions[] = array('from'=>($this->page), 'reset'=> $resetpagenum, 'type'=>$pagenumstyle, 'suppress'=>$suppress);
		}
  		// PAGED MEDIA - CROP / CROSS MARKS from @PAGE
		$this->show_marks = $marks;

		// Background color
		if (isset($bg['BACKGROUND-COLOR'])) {
			$cor = $this->ConvertColor($bg['BACKGROUND-COLOR']);
			if ($cor) { 
				$this->bodyBackgroundColor = $cor; 
			}
		}
		else { $this->bodyBackgroundColor = false; }

/*-- BACKGROUNDS --*/
		if (isset($bg['BACKGROUND-GRADIENT'])) { 
			$this->bodyBackgroundGradient = $bg['BACKGROUND-GRADIENT'];
		}
		else { $this->bodyBackgroundGradient = false; }

		// Tiling Patterns
		if (isset($bg['BACKGROUND-IMAGE']) && $bg['BACKGROUND-IMAGE']) { 
			$ret = $this->SetBackground($bg, $this->pgwidth);
			if ($ret) { $this->bodyBackgroundImage = $ret; }
		}
		else { $this->bodyBackgroundImage = false; }
/*-- END BACKGROUNDS --*/

		$this->page_box['current'] = $psel;
		$this->page_box['using'] = true;
	}
/*-- END CSS-PAGE --*/

	//Page orientation
	if(!$orientation)
		$orientation=$this->DefOrientation;
	else {
		$orientation=strtoupper(substr($orientation,0,1));
		if($orientation!=$this->DefOrientation)
			$this->OrientationChanges[$this->page]=true;
	}
	if($orientation!=$this->CurOrientation || $newformat) {

		//Change orientation
		if($orientation=='P') {
			$this->wPt=$this->fwPt;
			$this->hPt=$this->fhPt;
			$this->w=$this->fw;
			$this->h=$this->fh;
		   if (($this->forcePortraitHeaders || $this->forcePortraitMargins) && $this->DefOrientation=='P') {
			$this->tMargin = $this->orig_tMargin;
			$this->bMargin = $this->orig_bMargin;
			$this->DeflMargin = $this->orig_lMargin;
			$this->DefrMargin = $this->orig_rMargin;
			$this->margin_header = $this->orig_hMargin;
			$this->margin_footer = $this->orig_fMargin;
		   }
		   else { $resetHTMLHeadersrequired = true; }	// *HTMLHEADERS-FOOTERS*
		}
		else {
			$this->wPt=$this->fhPt;
			$this->hPt=$this->fwPt;
			$this->w=$this->fh;
			$this->h=$this->fw;
		   if (($this->forcePortraitHeaders || $this->forcePortraitMargins) && $this->DefOrientation=='P') {
			$this->tMargin = $this->orig_lMargin;
			$this->bMargin = $this->orig_rMargin;
			$this->DeflMargin = $this->orig_bMargin;
			$this->DefrMargin = $this->orig_tMargin;
			$this->margin_header = $this->orig_hMargin;
			$this->margin_footer = $this->orig_fMargin;
		   }
		   else { $resetHTMLHeadersrequired = true; }	// *HTMLHEADERS-FOOTERS*

		}
		$this->CurOrientation=$orientation;
		$this->ResetMargins();
		$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
		$this->PageBreakTrigger=$this->h-$this->bMargin;
	}

	$this->pageDim[$this->page]['w']=$this->w ;
	$this->pageDim[$this->page]['h']=$this->h ;

	$this->pageDim[$this->page]['outer_width_LR'] = isset($this->page_box['outer_width_LR']) ? $this->page_box['outer_width_LR'] : 0; 
	$this->pageDim[$this->page]['outer_width_TB'] = isset($this->page_box['outer_width_TB']) ? $this->page_box['outer_width_TB'] : 0; 
	if (!isset($this->page_box['outer_width_LR']) && !isset($this->page_box['outer_width_TB'])) {
		$this->pageDim[$this->page]['bleedMargin'] = 0;
	}
	else if ($this->bleedMargin <= $this->page_box['outer_width_LR'] && $this->bleedMargin <= $this->page_box['outer_width_TB']) {
		$this->pageDim[$this->page]['bleedMargin'] = $this->bleedMargin;
	}
	else {
		$this->pageDim[$this->page]['bleedMargin'] = min($this->page_box['outer_width_LR'], $this->page_box['outer_width_TB'])-0.01;
	}

	// If Page Margins are re-defined
	// strlen()>0 is used to pick up (integer) 0, (string) '0', or set value
	if ((strlen($mgl)>0 && $this->DeflMargin != $mgl) || (strlen($mgr)>0 && $this->DefrMargin != $mgr) || (strlen($mgt)>0 && $this->tMargin != $mgt) || (strlen($mgb)>0 && $this->bMargin != $mgb) || (strlen($mgh)>0 && $this->margin_header!=$mgh) || (strlen($mgf)>0 && $this->margin_footer!=$mgf)) {
		if (strlen($mgl)>0)  $this->DeflMargin = $mgl;
		if (strlen($mgr)>0)  $this->DefrMargin = $mgr;
		if (strlen($mgt)>0)  $this->tMargin = $mgt;
		if (strlen($mgb)>0)  $this->bMargin = $mgb;
		if (strlen($mgh)>0)  $this->margin_header=$mgh;
		if (strlen($mgf)>0)  $this->margin_footer=$mgf;
		$this->ResetMargins();
		$this->SetAutoPageBreak($this->autoPageBreak,$this->bMargin);
		$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
		$resetHTMLHeadersrequired = true; 	// *HTMLHEADERS-FOOTERS*
	}

	$this->ResetMargins();
	$this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
	$this->SetAutoPageBreak($this->autoPageBreak,$this->bMargin);

	// Reset column top margin
	$this->y0 = $this->tMargin;

	$this->x=$this->lMargin;
	$this->y=$this->tMargin;
	$this->FontFamily='';

	// HEADERS AND FOOTERS
	if ($ohvalue<0 || strtoupper($ohvalue)=='OFF') { 
		$this->HTMLHeader = ''; 
		$this->headerDetails['odd'] = array(); 
		$resetHTMLHeadersrequired = true;	// *HTMLHEADERS-FOOTERS*
	}
	else if ($ohname && $ohvalue>0) {
/*-- HTMLHEADERS-FOOTERS --*/
	   if (preg_match('/^html_(.*)$/i',$ohname,$n)) {
		if (isset($this->pageHTMLheaders[$n[1]])) { $this->HTMLHeader = $this->pageHTMLheaders[$n[1]]; }
		else { $this->HTMLHeader = ''; }
		$this->headerDetails['odd'] = array(); 
		$resetHTMLHeadersrequired = true;
	   }
	   else {
/*-- END HTMLHEADERS-FOOTERS --*/
		if (isset($this->pageheaders[$ohname])) { $this->headerDetails['odd'] = $this->pageheaders[$ohname]; } 
		else if ($ohname!='_default') { $this->headerDetails['odd'] = array(); }
		$this->HTMLHeader = ''; 
/*-- HTMLHEADERS-FOOTERS --*/
		$resetHTMLHeadersrequired = false;
	   }
/*-- END HTMLHEADERS-FOOTERS --*/
	}

	if ($ehvalue<0 || strtoupper($ehvalue)=='OFF') { 
		$this->HTMLHeaderE = ''; 
		$this->headerDetails['even'] = array(); 
		$resetHTMLHeadersrequired = true;	// *HTMLHEADERS-FOOTERS*
	}
	else if ($ehname && $ehvalue>0) {
/*-- HTMLHEADERS-FOOTERS --*/
	   if (preg_match('/^html_(.*)$/i',$ehname,$n)) {
		if (isset($this->pageHTMLheaders[$n[1]])) { $this->HTMLHeaderE = $this->pageHTMLheaders[$n[1]]; } 
		else { $this->HTMLHeaderE = ''; }
		$this->headerDetails['even'] = array(); 
		$resetHTMLHeadersrequired = true;
	   }
	   else {
/*-- END HTMLHEADERS-FOOTERS --*/
		if (isset($this->pageheaders[$ehname])) { $this->headerDetails['even'] = $this->pageheaders[$ehname]; }
		else if ($ehname!='_default') { $this->headerDetails['even'] = array(); }
		$this->HTMLHeaderE = ''; 
/*-- HTMLHEADERS-FOOTERS --*/
		$resetHTMLHeadersrequired = false;
	   }
/*-- END HTMLHEADERS-FOOTERS --*/
	}

	if ($ofvalue<0 || strtoupper($ofvalue)=='OFF') { 
		$this->HTMLFooter = ''; 
		$this->footerDetails['odd'] = array(); 
		$resetHTMLHeadersrequired = true;	// *HTMLHEADERS-FOOTERS*
	}
	else if ($ofname && $ofvalue>0) {
/*-- HTMLHEADERS-FOOTERS --*/
	   if (preg_match('/^html_(.*)$/i',$ofname,$n)) {
		if (isset($this->pageHTMLfooters[$n[1]])) { $this->HTMLFooter = $this->pageHTMLfooters[$n[1]]; }
		else { $this->HTMLFooter = ''; }
		$this->footerDetails['odd'] = array(); 
		$resetHTMLHeadersrequired = true;
	   }
	   else {
/*-- END HTMLHEADERS-FOOTERS --*/
		if (isset($this->pagefooters[$ofname])) { $this->footerDetails['odd'] = $this->pagefooters[$ofname]; }
		else if ($ofname!='_default') { $this->footerDetails['odd'] = array(); }
		$this->HTMLFooter = ''; 
/*-- HTMLHEADERS-FOOTERS --*/
		$resetHTMLHeadersrequired = true;
	   }
/*-- END HTMLHEADERS-FOOTERS --*/
	}

	if ($efvalue<0 || strtoupper($efvalue)=='OFF') { 
		$this->HTMLFooterE = ''; 
		$this->footerDetails['even'] = array(); 
		$resetHTMLHeadersrequired = true;	// *HTMLHEADERS-FOOTERS*
	}
	else if ($efname && $efvalue>0) {
/*-- HTMLHEADERS-FOOTERS --*/
	   if (preg_match('/^html_(.*)$/i',$efname,$n)) {
		if (isset($this->pageHTMLfooters[$n[1]])) { $this->HTMLFooterE = $this->pageHTMLfooters[$n[1]]; } 
		else { $this->HTMLFooterE = ''; }
		$this->footerDetails['even'] = array(); 
		$resetHTMLHeadersrequired = true;
	   }
	   else {
/*-- END HTMLHEADERS-FOOTERS --*/
		if (isset($this->pagefooters[$efname])) { $this->footerDetails['even'] = $this->pagefooters[$efname]; } 
		else if ($efname!='_default') { $this->footerDetails['even'] = array(); }
		$this->HTMLFooterE = ''; 
/*-- HTMLHEADERS-FOOTERS --*/
		$resetHTMLHeadersrequired = true;
	   }
/*-- END HTMLHEADERS-FOOTERS --*/
	}

/*-- HTMLHEADERS-FOOTERS --*/
	if ($resetHTMLHeadersrequired) {
		$this->SetHTMLHeader($this->HTMLHeader );
		$this->SetHTMLHeader($this->HTMLHeaderE ,'E');
		$this->SetHTMLFooter($this->HTMLFooter );
		$this->SetHTMLFooter($this->HTMLFooterE ,'E');
	}
/*-- END HTMLHEADERS-FOOTERS --*/


	if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
		$this->_setAutoHeaderHeight($this->headerDetails['even'], $this->HTMLHeaderE);
		$this->_setAutoFooterHeight($this->footerDetails['even'], $this->HTMLFooterE);
	}
	else {	// ODD or DEFAULT
		$this->_setAutoHeaderHeight($this->headerDetails['odd'], $this->HTMLHeader);
		$this->_setAutoFooterHeight($this->footerDetails['odd'], $this->HTMLFooter);
	}
	// Reset column top margin
	$this->y0 = $this->tMargin;

	$this->x=$this->lMargin;
	$this->y=$this->tMargin;
}



function _setAutoHeaderHeight(&$det, &$htmlh) {
  if ($this->setAutoTopMargin=='pad') {
	if ($htmlh['h']) { $h = $htmlh['h']; }
	else if ($det) { $h = $this->_getHFHeight($det,'H'); }
	else { $h = 0; }
	$this->tMargin = $this->margin_header + $h + $this->orig_tMargin;
  }
  else if ($this->setAutoTopMargin=='stretch') {
	if ($htmlh['h']) { $h = $htmlh['h']; }
	else if ($det) { $h = $this->_getHFHeight($det,'H'); }
	else { $h = 0; }
	$this->tMargin = max($this->orig_tMargin, $this->margin_header + $h + $this->autoMarginPadding);
  }
}


function _setAutoFooterHeight(&$det, &$htmlf) {
  if ($this->setAutoBottomMargin=='pad') {
	if ($htmlf['h']) { $h = $htmlf['h']; }
	else if ($det) { $h = $this->_getHFHeight($det,'F'); }
	else { $h = 0; }
	$this->bMargin = $this->margin_footer + $h + $this->orig_bMargin;
	$this->PageBreakTrigger=$this->h-$this->bMargin ;
  }
  else if ($this->setAutoBottomMargin=='stretch') {
	if ($htmlf['h']) { $h = $htmlf['h']; }
	else if ($det) { $h = $this->_getHFHeight($det,'F'); }
	else { $h = 0; }
	$this->bMargin = max($this->orig_bMargin, $this->margin_footer + $h + $this->autoMarginPadding);
	$this->PageBreakTrigger=$this->h-$this->bMargin ;
  }
}

function _getHFHeight(&$det,$end) {
	$h = 0;
	if(count($det)) {
		foreach(array('L','C','R') AS $pos) {
		  if (isset($det[$pos]['content']) && $det[$pos]['content']) {
			if (isset($det[$pos]['font-size']) && $det[$pos]['font-size']) { $hfsz = $det[$pos]['font-size']; }
			else { $hfsz = $this->default_font_size; }
			$h = max($h,$hfsz/_MPDFK);
		  }
		}
		if ($det['line'] && $end=='H') { $h += $h/_MPDFK*$this->header_line_spacing; }
		else if ($det['line'] && $end=='F') { $h += $h/_MPDFK*$this->footer_line_spacing; }
   	}
	return $h;
}


function _endpage() {
/*-- CSS-IMAGE-FLOAT --*/
	$this->printfloatbuffer();
/*-- END CSS-IMAGE-FLOAT --*/

	// mPDF 5.3.41
	if($this->visibility!='visible')
		$this->SetVisibility('visible');

	//End of page contents
	$this->state=1;
}

function _newobj($obj_id=false,$onlynewobj=false) {
		if (!$obj_id) {
			$obj_id = ++$this->n;
		}
		//Begin a new object
		if (!$onlynewobj) {
			$this->offsets[$obj_id] = strlen($this->buffer);
			$this->_out($obj_id.' 0 obj');
			$this->_current_obj_id = $obj_id; // for later use with encryption
		}
}

function _dounderline($x,$y,$txt) {
	// Now print line exactly where $y secifies - called from Text() and Cell() - adjust  position there
	// WORD SPACING
      $w =($this->GetStringWidth($txt)*_MPDFK) + ($this->charspacing * mb_strlen( $txt, $this->mb_enc )) 
		 + ( $this->ws * mb_substr_count( $txt, ' ', $this->mb_enc ));
	//Draw a line
	return sprintf('%.3F %.3F m %.3F %.3F l S',$x*_MPDFK,($this->h-$y)*_MPDFK,($x*_MPDFK)+$w,($this->h-$y)*_MPDFK);
}


function _imageError($file, $firsttime, $msg) {
	// Save re-trying image URL's which have already failed
	$this->failedimages[$file] = true;
	if ($firsttime && ($this->showImageErrors || $this->debug)) {
			$this->Error("IMAGE Error (".$file."): ".$msg);
	}
	return false;
}


function _getImage(&$file, $firsttime=true, $allowvector=true, $orig_srcpath=false) { 
	// firsttime i.e. whether to add to this->images - use false when calling iteratively
	// mPDF 5.3.95
	$file = urldecode($file);
	if ($orig_srcpath) { $orig_srcpath = urldecode($orig_srcpath); }
	// Image Data passed directly as var:varname
	if (preg_match('/var:\s*(.*)/',$file, $v)) { 
		$data = $this->$v[1];
		$file = md5($data);
	}
	$ppUx = 0;
	if ($firsttime && preg_match('/(.*\/)([^\/]*)/',$file,$fm)) {
		if (strlen($fm[2])) { $file = $fm[1].preg_replace('/ /','%20',$fm[2]); }
	}
	if ($orig_srcpath && isset($this->images[$orig_srcpath])) { $file=$orig_srcpath; return $this->images[$orig_srcpath]; }
	if (isset($this->images[$file])) { return $this->images[$file]; }
	else if ($orig_srcpath && isset($this->formobjects[$orig_srcpath])) { $file=$orig_srcpath; return $this->formobjects[$file]; }
	else if (isset($this->formobjects[$file])) { return $this->formobjects[$file]; }
	// Save re-trying image URL's which have already failed
	else if ($firsttime && isset($this->failedimages[$file])) { return $this->_imageError($file, $firsttime, ''); } 
	if (empty($data)) {
		$type = '';
		$data = '';
		$mqr=$this->_getMQR();
		if ($mqr) { set_magic_quotes_runtime(0); }

 		if ($orig_srcpath && $this->basepathIsLocal && $check = @fopen($orig_srcpath,"rb")) {
			fclose($check); 
			$file=$orig_srcpath;
			$data = file_get_contents($file);
			$type = $this->_imageTypeFromString($data);
		}
		if (!$data && $check = @fopen($file,"rb")) { 
			fclose($check); 
			$data = file_get_contents($file);
			$type = $this->_imageTypeFromString($data);
		}
		if ((!$data || !$type) && !ini_get('allow_url_fopen')) {	// only worth trying if remote file and !ini_get('allow_url_fopen')
			$this->file_get_contents_by_socket($file, $data);	// needs full url?? even on local (never needed for local)
			if ($data) { $type = $this->_imageTypeFromString($data); }
		}
		if ((!$data || !$type) && !ini_get('allow_url_fopen') && function_exists("curl_init")) {
			$this->file_get_contents_by_curl($file, $data);		// needs full url?? even on local (never needed for local)
			if ($data) { $type = $this->_imageTypeFromString($data); }
		}

		if ($mqr) { set_magic_quotes_runtime($mqr); }
	}
	if (!$data) { return $this->_imageError($file, $firsttime, 'Could not find image file'); }
	if (empty($type)) { $type = $this->_imageTypeFromString($data); }	
	if (($type == 'wmf' || $type == 'svg') && !$allowvector) { return $this->_imageError($file, $firsttime, 'WMF or SVG image file not supported in this context'); }

	// SVG
	if ($type == 'svg') {
		if (!class_exists('SVG', false)) { include(_MPDF_PATH .'classes/svg.php'); }
		$svg = new SVG($this);
		$family=$this->FontFamily;
		$style=$this->FontStyle;
		$size=$this->FontSizePt;
		$info = $svg->ImageSVG($data);
		//Restore font
		if($family) $this->SetFont($family,$style,$size,false);
		if (!$info) { return $this->_imageError($file, $firsttime, 'Error parsing SVG file'); }
		$info['type']='svg';
		$info['i']=count($this->formobjects)+1;
		$this->formobjects[$file]=$info;
		return $info;
	}

	// JPEG
	if ($type == 'jpeg' || $type == 'jpg') {
		$hdr = $this->_jpgHeaderFromString($data);
		if (!$hdr) { return $this->_imageError($file, $firsttime, 'Error parsing JPG header'); }
		$a = $this->_jpgDataFromHeader($hdr);
		$j = strpos($data,'JFIF');
		if ($j) { 
			//Read resolution
			$unitSp=ord(substr($data,($j+7),1));
			if ($unitSp > 0) {
				$ppUx=$this->_twobytes2int(substr($data,($j+8),2));	// horizontal pixels per meter, usually set to zero
				if ($unitSp == 2) {	// = dots per cm (if == 1 set as dpi)
					$ppUx=round($ppUx/10 *25.4);
				}
			}
		}
		if ($a[2] == 'DeviceCMYK' && (($this->PDFA && $this->restrictColorSpace!=3) || $this->restrictColorSpace==2)) {
			// convert to RGB image
			if (!function_exists("gd_info")) { $this->Error("JPG image may not use CMYK color space (".$file.")."); }
			if ($this->PDFA && !$this->PDFAauto) { $this->PDFAXwarnings[] = "JPG image may not use CMYK color space - ".$file." - (Image converted to RGB. NB This will alter the colour profile of the image.)"; }
			$im = @imagecreatefromstring($data);
			if ($im) {
				$tempfile = _MPDF_TEMP_PATH.'_tempImgPNG'.RAND(1,10000).'.png';
				imageinterlace($im, false);
				$check = @imagepng($im, $tempfile);
				if (!$check) { return $this->_imageError($file, $firsttime, 'Error creating temporary file ('.$tempfile.') whilst using GD library to parse JPG(CMYK) image'); }
				$info = $this->_getImage($tempfile, false);
				if (!$info) { return $this->_imageError($file, $firsttime, 'Error parsing temporary file ('.$tempfile.') created with GD library to parse JPG(CMYK) image'); }
				imagedestroy($im);
				unlink($tempfile);
				$info['type']='jpg';
				if ($firsttime) {
					$info['i']=count($this->images)+1;
					$this->images[$file]=$info;
				}
				return $info;
			}
			else { return $this->_imageError($file, $firsttime, 'Error creating GD image file from JPG(CMYK) image'); }
		}
		else if ($a[2] == 'DeviceRGB' && ($this->PDFX || $this->restrictColorSpace==3)) {
			// Convert to CMYK image stream - nominally returned as type='png'
			$info = $this->_convImage($data, $a[2], 'DeviceCMYK', $a[0], $a[1], $ppUx, false);
			if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) { $this->PDFAXwarnings[] = "JPG image may not use RGB color space - ".$file." - (Image converted to CMYK. NB This will alter the colour profile of the image.)"; }
		}
		else if (($a[2] == 'DeviceRGB' || $a[2] == 'DeviceCMYK') && $this->restrictColorSpace==1) {
			// Convert to Grayscale image stream - nominally returned as type='png'
			$info = $this->_convImage($data, $a[2], 'DeviceGray', $a[0], $a[1], $ppUx, false);
		}
		else {
			$info = array('w'=>$a[0],'h'=>$a[1],'cs'=>$a[2],'bpc'=>$a[3],'f'=>'DCTDecode','data'=>$data, 'type'=>'jpg');
			if ($ppUx) { $info['set-dpi'] = $ppUx; }
		}
		if (!$info) { return $this->_imageError($file, $firsttime, 'Error parsing or converting JPG image'); }

		if ($firsttime) {
			$info['i']=count($this->images)+1;
			$this->images[$file]=$info;
		}
		return $info;
	}

	// PNG
	else if ($type == 'png') {
		//Check signature
		if(substr($data,0,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) { 
			return $this->_imageError($file, $firsttime, 'Error parsing PNG identifier'); 
		}
		//Read header chunk
		if(substr($data,12,4)!='IHDR') { 
			return $this->_imageError($file, $firsttime, 'Incorrect PNG file (no IHDR block found)'); 
		}

		$w=$this->_fourbytes2int(substr($data,16,4));
		$h=$this->_fourbytes2int(substr($data,20,4));
		$bpc=ord(substr($data,24,1));
		$errpng = false;
		$pngalpha = false; 
		if($bpc>8) { $errpng = 'not 8-bit depth'; }
		$ct=ord(substr($data,25,1));
		if($ct==0) { $colspace='DeviceGray'; }
		elseif($ct==2) { $colspace='DeviceRGB'; }
		elseif($ct==3) { $colspace='Indexed'; }
		elseif($ct==4) { $colspace='DeviceGray';  $errpng = 'alpha channel'; $pngalpha = true; }
		else { $colspace='DeviceRGB'; $errpng = 'alpha channel'; $pngalpha = true; } 
		if(ord(substr($data,26,1))!=0) { $errpng = 'compression method'; }
		if(ord(substr($data,27,1))!=0) { $errpng = 'filter method'; }
		if(ord(substr($data,28,1))!=0) { $errpng = 'interlaced file'; }
		$j = strpos($data,'pHYs');
		if ($j) { 
			//Read resolution
			$unitSp=ord(substr($data,($j+12),1));
			if ($unitSp == 1) {
				$ppUx=$this->_fourbytes2int(substr($data,($j+4),4));	// horizontal pixels per meter, usually set to zero
				$ppUx=round($ppUx/1000 *25.4);
			}
		}
		if (($colspace == 'DeviceRGB' || $colspace == 'Indexed') && ($this->PDFX || $this->restrictColorSpace==3)) {
			// Convert to CMYK image stream - nominally returned as type='png'
			$info = $this->_convImage($data, $colspace, 'DeviceCMYK', $w, $h, $ppUx, $pngalpha);
			if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) { $this->PDFAXwarnings[] = "PNG image may not use RGB color space - ".$file." - (Image converted to CMYK. NB This will alter the colour profile of the image.)"; }
		}
		else if (($colspace == 'DeviceRGB' || $colspace == 'Indexed') && $this->restrictColorSpace==1) {
			// Convert to Grayscale image stream - nominally returned as type='png'
			$info = $this->_convImage($data, $colspace, 'DeviceGray', $w, $h, $ppUx, $pngalpha);
		}
		else if (($this->PDFA || $this->PDFX) && $pngalpha) {
			// Remove alpha channel
			if ($this->restrictColorSpace==1) {	// Grayscale
				$info = $this->_convImage($data, $colspace, 'DeviceGray', $w, $h, $ppUx, $pngalpha);
			}
			else if ($this->restrictColorSpace==3) {	// CMYK
				$info = $this->_convImage($data, $colspace, 'DeviceCMYK', $w, $h, $ppUx, $pngalpha);
			}
			else if ($this->PDFA ) {	// RGB
				$info = $this->_convImage($data, $colspace, 'DeviceRGB', $w, $h, $ppUx, $pngalpha);
			}
			if (($this->PDFA && !$this->PDFAauto) || ($this->PDFX && !$this->PDFXauto)) { $this->PDFAXwarnings[] = "Transparency (alpha channel) not permitted in PDFA or PDFX files - ".$file." - (Image converted to one without transparency.)"; }
		}
		else if ($errpng || $pngalpha) {
			if (function_exists('gd_info')) { $gd = gd_info(); }
			else {$gd = array(); }
			if (!isset($gd['PNG Support'])) { return $this->_imageError($file, $firsttime, 'GD library required for PNG image ('.$errpng.')'); }
			$im = imagecreatefromstring($data);
			if (!$im) { return $this->_imageError($file, $firsttime, 'Error creating GD image from PNG file ('.$errpng.')'); }
			$w = imagesx($im);
			$h = imagesy($im);
			if ($im) {
			   $tempfile = _MPDF_TEMP_PATH.'_tempImgPNG'.RAND(1,10000).'.png';
			   // Alpha channel set
			   if ($pngalpha) {
				if ($this->PDFA) { $this->Error("PDFA1-b does not permit images with alpha channel transparency (".$file.")."); }
				$imgalpha = imagecreate($w, $h);
				// generate gray scale pallete
				for ($c = 0; $c < 256; ++$c) { ImageColorAllocate($imgalpha, $c, $c, $c); }
				// extract alpha channel
				$gammacorr = 2.2;	// gamma correction	// mPDF 5.3.07
				for ($xpx = 0; $xpx < $w; ++$xpx) {
					for ($ypx = 0; $ypx < $h; ++$ypx) {
						// mPDF 5.3.07
						//$colorindex = imagecolorat($im, $xpx, $ypx);
						//$col = imagecolorsforindex($im, $colorindex);
						//$gamma2 = (pow((((127 - $col['alpha']) * 255 / 127) / 255), $gammacorr) * 255);
						$alpha = (imagecolorat($im, $xpx, $ypx) & 0x7F000000) >> 24;
						if ($alpha < 127) {
							if ($alpha==0) { $gamma = 255; }
							else
								$gamma = (pow((((127 - $alpha) * 255 / 127) / 255), $gammacorr) * 255);
							imagesetpixel($imgalpha, $xpx, $ypx, $gamma);
						}
					}
				}
				// create temp alpha file
	 		 	$tempfile_alpha = _MPDF_TEMP_PATH.'_tempMskPNG'.RAND(1,10000).'.png';
				if (!is_writable($tempfile_alpha)) { 
					ob_start(); 
					$check = @imagepng($imgalpha);
					if (!$check) { return $this->_imageError($file, $firsttime, 'Error creating temporary image object whilst using GD library to parse PNG image'); }
					imagedestroy($imgalpha);
					$this->_tempimg = ob_get_contents();
					$this->_tempimglnk = 'var:_tempimg';
					ob_end_clean();
					// extract image without alpha channel
					$imgplain = imagecreatetruecolor($w, $h);
					imagecopy($imgplain, $im, 0, 0, 0, 0, $w, $h);
					// create temp image file
					$minfo = $this->_getImage($this->_tempimglnk, false);
					if (!$minfo) { return $this->_imageError($file, $firsttime, 'Error parsing temporary file image object created with GD library to parse PNG image'); }
					ob_start(); 
					$check = @imagepng($imgplain);
					if (!$check) { return $this->_imageError($file, $firsttime, 'Error creating temporary image object whilst using GD library to parse PNG image'); }
					$this->_tempimg = ob_get_contents();
					$this->_tempimglnk = 'var:_tempimg';
					ob_end_clean();
					$info = $this->_getImage($this->_tempimglnk, false);
					if (!$info) { return $this->_imageError($file, $firsttime, 'Error parsing temporary file image object created with GD library to parse PNG image'); }
					imagedestroy($imgplain);
					$imgmask = count($this->images)+1;
					$minfo['cs'] = 'DeviceGray';
					$minfo['i']=$imgmask ;
					$this->images[$tempfile_alpha] = $minfo;

				}
				else {
					$check = @imagepng($imgalpha, $tempfile_alpha);
					if (!$check) { return $this->_imageError($file, $firsttime, 'Failed to create temporary image file ('.$tempfile_alpha.') parsing PNG image with alpha channel ('.$errpng.')'); }
					imagedestroy($imgalpha);

					// extract image without alpha channel
					$imgplain = imagecreatetruecolor($w, $h);
					imagecopy($imgplain, $im, 0, 0, 0, 0, $w, $h);

					// create temp image file
					$check = @imagepng($imgplain, $tempfile);
					if (!$check) { return $this->_imageError($file, $firsttime, 'Failed to create temporary image file ('.$tempfile.') parsing PNG image with alpha channel ('.$errpng.')'); }
					imagedestroy($imgplain);
					// embed mask image
					$minfo = $this->_getImage($tempfile_alpha, false);
					unlink($tempfile_alpha);
					if (!$minfo) { return $this->_imageError($file, $firsttime, 'Error parsing temporary file ('.$tempfile_alpha.') created with GD library to parse PNG image'); }
					$imgmask = count($this->images)+1;
					$minfo['cs'] = 'DeviceGray';
					$minfo['i']=$imgmask ;
					$this->images[$tempfile_alpha] = $minfo;
					// embed image, masked with previously embedded mask
					$info = $this->_getImage($tempfile, false);
					unlink($tempfile);
					if (!$info) { return $this->_imageError($file, $firsttime, 'Error parsing temporary file ('.$tempfile.') created with GD library to parse PNG image'); }

				}
				$info['masked'] = $imgmask;
				if ($ppUx) { $info['set-dpi'] = $ppUx; }
				$info['type']='png';
				if ($firsttime) {
					$info['i']=count($this->images)+1;
					$this->images[$file]=$info;
				}
				return $info;
			   }
			   else { 	// No alpha/transparency set
				imagealphablending($im, false);
				imagesavealpha($im, false); 
				imageinterlace($im, false);
				if (!is_writable($tempfile)) { 
					ob_start(); 
					$check = @imagepng($im);
					if (!$check) { return $this->_imageError($file, $firsttime, 'Error creating temporary image object whilst using GD library to parse PNG image'); }
					$this->_tempimg = ob_get_contents();
					$this->_tempimglnk = 'var:_tempimg';
					ob_end_clean();
					$info = $this->_getImage($this->_tempimglnk, false);
					if (!$info) { return $this->_imageError($file, $firsttime, 'Error parsing temporary file image object created with GD library to parse PNG image'); }
					imagedestroy($im);
				}
				else {
					$check = @imagepng($im, $tempfile );
					if (!$check) { return $this->_imageError($file, $firsttime, 'Failed to create temporary image file ('.$tempfile.') parsing PNG image ('.$errpng.')'); }
					imagedestroy($im);
					$info = $this->_getImage($tempfile, false) ;
					unlink($tempfile ); 
					if (!$info) { return $this->_imageError($file, $firsttime, 'Error parsing temporary file ('.$tempfile.') created with GD library to parse PNG image'); }
				}
				if ($ppUx) { $info['set-dpi'] = $ppUx; }
				$info['type']='png';
				if ($firsttime) {
					$info['i']=count($this->images)+1;
					$this->images[$file]=$info;
				}
				return $info;
			   }
			}
		}

		else {
			$parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
			//Scan chunks looking for palette, transparency and image data
			$pal='';
			$trns='';
			$pngdata='';
			$p = 33;
			do {
				$n=$this->_fourbytes2int(substr($data,$p,4));	$p += 4;
				$type=substr($data,$p,4);	$p += 4;
				if($type=='PLTE') {
					//Read palette
					$pal=substr($data,$p,$n);	$p += $n;
					$p += 4;
				}
				elseif($type=='tRNS') {
					//Read transparency info
					$t=substr($data,$p,$n);	$p += $n;
					if($ct==0) $trns=array(ord(substr($t,1,1)));
					elseif($ct==2) $trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
					else
					{
						$pos=strpos($t,chr(0));
						if(is_int($pos)) $trns=array($pos);
					}
					$p += 4;
				}
				elseif($type=='IDAT') {
					$pngdata.=substr($data,$p,$n);	$p += $n;
					$p += 4;
				}
				elseif($type=='IEND') { break; }
				else if (preg_match('/[a-zA-Z]{4}/',$type)) { $p += $n+4; }
				else { return $this->_imageError($file, $firsttime, 'Error parsing PNG image data'); }
			}
			while($n);
			if (!$pngdata) { return $this->_imageError($file, $firsttime, 'Error parsing PNG image data - no IDAT data found'); }
			if($colspace=='Indexed' and empty($pal)) { return $this->_imageError($file, $firsttime, 'Error parsing PNG image data - missing colour palette'); }
			$info = array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$pngdata);
			$info['type']='png';
			if ($ppUx) { $info['set-dpi'] = $ppUx; }
		}

		if (!$info) { return $this->_imageError($file, $firsttime, 'Error parsing or converting PNG image'); }

		if ($firsttime) {
			$info['i']=count($this->images)+1;
			$this->images[$file]=$info;
		}
		return $info;
	}

	// GIF
	else if ($type == 'gif') {
	if (function_exists('gd_info')) { $gd = gd_info(); }
		else {$gd = array(); }
		if (isset($gd['GIF Read Support']) && $gd['GIF Read Support']) {
			$im = @imagecreatefromstring($data);
			if ($im) {
				$tempfile = _MPDF_TEMP_PATH.'_tempImgPNG'.RAND(1,10000).'.png';
				imagealphablending($im, false);
				imagesavealpha($im, false); 
				imageinterlace($im, false);
				if (!is_writable($tempfile)) { 
					ob_start(); 
					$check = @imagepng($im);
					if (!$check) { return $this->_imageError($file, $firsttime, 'Error creating temporary image object whilst using GD library to parse GIF image'); }
					$this->_tempimg = ob_get_contents();
					$this->_tempimglnk = 'var:_tempimg';
					ob_end_clean();
					$info = $this->_getImage($this->_tempimglnk, false);
					if (!$info) { return $this->_imageError($file, $firsttime, 'Error parsing temporary file image object created with GD library to parse GIF image'); }
					imagedestroy($im);
				}
				else {
					$check = @imagepng($im, $tempfile);
					if (!$check) { return $this->_imageError($file, $firsttime, 'Error creating temporary file ('.$tempfile.') whilst using GD library to parse GIF image'); }
					$info = $this->_getImage($tempfile, false);
					if (!$info) { return $this->_imageError($file, $firsttime, 'Error parsing temporary file ('.$tempfile.') created with GD library to parse GIF image'); }
					imagedestroy($im);
					unlink($tempfile);
				}
				$info['type']='gif';
				if ($firsttime) {
					$info['i']=count($this->images)+1;
					$this->images[$file]=$info;
				}
				return $info;
			}
			else { return $this->_imageError($file, $firsttime, 'Error creating GD image file from GIF image'); }
		}

		if (!class_exists('gif', false)) { 
			include_once(_MPDF_PATH.'classes/gif.php'); 
		}
		$gif=new CGIF();

		$h=0;
		$w=0;
		$gif->loadFile($data, 0);

		if(isset($gif->m_img->m_gih->m_bLocalClr) && $gif->m_img->m_gih->m_bLocalClr) {
			$nColors = $gif->m_img->m_gih->m_nTableSize;
			$pal = $gif->m_img->m_gih->m_colorTable->toString();
			if($bgColor != -1) {
				$bgColor = $gif->m_img->m_gih->m_colorTable->colorIndex($bgColor);
			}
			$colspace='Indexed';
		} elseif(isset($gif->m_gfh->m_bGlobalClr) && $gif->m_gfh->m_bGlobalClr) {
			$nColors = $gif->m_gfh->m_nTableSize;
			$pal = $gif->m_gfh->m_colorTable->toString();
			if((isset($bgColor)) and $bgColor != -1) {
				$bgColor = $gif->m_gfh->m_colorTable->colorIndex($bgColor);
			}
			$colspace='Indexed';
		} else {
			$nColors = 0;
			$bgColor = -1;
			$colspace='DeviceGray';
			$pal='';
		}

		$trns='';
		if(isset($gif->m_img->m_bTrans) && $gif->m_img->m_bTrans && ($nColors > 0)) {
			$trns=array($gif->m_img->m_nTrans);
		}
		$gifdata=$gif->m_img->m_data;
		$w=$gif->m_gfh->m_nWidth;
		$h=$gif->m_gfh->m_nHeight;
		$gif->ClearData();

		if($colspace=='Indexed' and empty($pal)) {
			return $this->_imageError($file, $firsttime, 'Error parsing GIF image - missing colour palette'); 
		}
		if ($this->compress) {
			$gifdata=gzcompress($gifdata);
			$info = array( 'w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>8, 'f'=>'FlateDecode', 'pal'=>$pal, 'trns'=>$trns, 'data'=>$gifdata);
		} 
		else {
			$info = array( 'w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>8, 'pal'=>$pal, 'trns'=>$trns, 'data'=>$gifdata);
		} 
		$info['type']='gif';
		if ($firsttime) {
			$info['i']=count($this->images)+1;
			$this->images[$file]=$info;
		}
		return $info;
	}

/*-- IMAGES-BMP --*/
	// BMP (Windows Bitmap)
	else if ($type == 'bmp') {
		// mPDF 5.3.89
		if (!class_exists('bmp', false)) { include(_MPDF_PATH.'classes/bmp.php'); }
		if (empty($this->bmp)) { $this->bmp = new bmp($this); }
		$info = $this->bmp->_getBMPimage($data, $file);
		if (isset($info['error'])) {
			return $this->_imageError($file, $firsttime, $info['error']); 
		}
		if ($firsttime) {
			$info['i']=count($this->images)+1;
			$this->images[$file]=$info;
		}
		return $info;
	}
/*-- END IMAGES-BMP --*/
/*-- IMAGES-WMF --*/
	// WMF
	else if ($type == 'wmf') {
		// mPDF 5.3.89
		if (!class_exists('wmf', false)) { include(_MPDF_PATH.'classes/wmf.php'); }
		if (empty($this->wmf)) { $this->wmf = new wmf($this); }
		$wmfres = $this->wmf->_getWMFimage($data);
	  if ($wmfres[0]==0) { 
		if ($wmfres[1]) { return $this->_imageError($file, $firsttime, $wmfres[1]); }
		return $this->_imageError($file, $firsttime, 'Error parsing WMF image'); 
	  }
	  $info = array('x'=>$wmfres[2][0],'y'=>$wmfres[2][1],'w'=>$wmfres[3][0],'h'=>$wmfres[3][1],'data'=>$wmfres[1]);
	  $info['i']=count($this->formobjects)+1;
	  $info['type']='wmf';
	  $this->formobjects[$file]=$info;
	  return $info;
	}
/*-- END IMAGES-WMF --*/

	// UNKNOWN TYPE - try GD imagecreatefromstring
	else {
		if (function_exists('gd_info')) { $gd = gd_info(); }
		else {$gd = array(); }
		if (isset($gd['PNG Support']) && $gd['PNG Support']) {
			$im = @imagecreatefromstring($data);
			if (!$im) { return $this->_imageError($file, $firsttime, 'Error parsing image file - image type not recognised, and not supported by GD imagecreate'); }
			$tempfile = _MPDF_TEMP_PATH.'_tempImgPNG'.RAND(1,10000).'.png';
			imagealphablending($im, false);
			imagesavealpha($im, false); 
			imageinterlace($im, false);
			$check = @imagepng($im, $tempfile);
			if (!$check) { return $this->_imageError($file, $firsttime, 'Error creating temporary file ('.$tempfile.') whilst using GD library to parse unknown image type'); }
			$info = $this->_getImage($tempfile, false);
			imagedestroy($im);
			unlink($tempfile);
			if (!$info) { return $this->_imageError($file, $firsttime, 'Error parsing temporary file ('.$tempfile.') created with GD library to parse unknown image type'); }
			$info['type']='png';
			if ($firsttime) {
				$info['i']=count($this->images)+1;
				$this->images[$file]=$info;
			}
			return $info;
		}
	}

	return $this->_imageError($file, $firsttime, 'Error parsing image file - image type not recognised'); 
}
//==============================================================
function _convImage(&$data, $colspace, $targetcs, $w, $h, $dpi, $mask) {
	if ($this->PDFA || $this->PDFX) { $mask=false; }
	$im = @imagecreatefromstring($data);
	$info = array();
	if ($im) {
		$imgdata = '';
		$mimgdata = '';
		$minfo = array();
		//Read transparency info
		$trns=array();
		$trnsrgb = false;
		if (!$this->PDFA && !$this->PDFX) { 
		   $p = strpos($data,'tRNS');
		   if ($p) { 
			$n=$this->_fourbytes2int(substr($data,($p-4),4));
			$t = substr($data,($p+4),$n);	
			if ($colspace=='DeviceGray') { 
				$trns=array(ord(substr($t,1,1))); 
				$trnsrgb = array($trns[0],$trns[0],$trns[0]);
			}
			else if ($colspace=='DeviceRGB') { 
				$trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1))); 
				$trnsrgb = $trns;
				if ($targetcs=='DeviceCMYK') {
					$col = $this->rgb2cmyk(array(3,$trns[0],$trns[1],$trns[2]));
					$c1 = intval($col[1]*2.55);
					$c2 = intval($col[2]*2.55);
					$c3 = intval($col[3]*2.55);
					$c4 = intval($col[4]*2.55);
					$trns = array($c1,$c2,$c3,$c4);
				}
				else if ($targetcs=='DeviceGray') {
					$c = intval(($trns[0] * .21) + ($trns[1] * .71) + ($trns[2] * .07));
					$trns = array($c);
				}
			}
			else {	// Indexed
				$pos = strpos($t,chr(0));
				if (is_int($pos)) {
					$pal = imagecolorsforindex($im, $pos);
					$r = $pal['red'];
					$g = $pal['green'];
					$b = $pal['blue'];
					$trns=array($r,$g,$b);	// ****
					$trnsrgb = $trns;
					if ($targetcs=='DeviceCMYK') {
						$col = $this->rgb2cmyk(array(3,$r,$g,$b));
						$c1 = intval($col[1]*2.55);
						$c2 = intval($col[2]*2.55);
						$c3 = intval($col[3]*2.55);
						$c4 = intval($col[4]*2.55);
						$trns = array($c1,$c2,$c3,$c4);
					}
					else if ($targetcs=='DeviceGray') {
						$c = intval(($r * .21) + ($g * .71) + ($b * .07));
						$trns = array($c);
					}
				}
			}
		   }
		}
		for ($i = 0; $i < $h; $i++) {
			for ($j = 0; $j < $w; $j++) {
				$rgb = imagecolorat($im, $j, $i);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				if ($colspace=='Indexed') {
					$pal = imagecolorsforindex($im, $rgb);
					$r = $pal['red'];
					$g = $pal['green'];
					$b = $pal['blue'];
				}

				if ($targetcs=='DeviceCMYK') {
					$col = $this->rgb2cmyk(array(3,$r,$g,$b));
					$c1 = intval($col[1]*2.55);
					$c2 = intval($col[2]*2.55);
					$c3 = intval($col[3]*2.55);
					$c4 = intval($col[4]*2.55);
					if ($trnsrgb) {
						// original pixel was not set as transparent but processed color does match
						if ($trnsrgb!=array($r,$g,$b) && $trns==array($c1,$c2,$c3,$c4)) {
							if ($c4==0) { $c4=1; } else { $c4--; }
						}
					}
					$imgdata .= chr($c1).chr($c2).chr($c3).chr($c4);
				}
				else if ($targetcs=='DeviceGray') {
					$c = intval(($r * .21) + ($g * .71) + ($b * .07));
					if ($trnsrgb) {
						// original pixel was not set as transparent but processed color does match
						if ($trnsrgb!=array($r,$g,$b) && $trns==array($c)) {
							if ($c==0) { $c=1; } else { $c--; }
						}
					}
					$imgdata .= chr($c);
				}
				else if ($targetcs=='DeviceRGB') {
					$imgdata .= chr($r).chr($g).chr($b);
				}
				if ($mask) {
					$col = imagecolorsforindex($im, $rgb);
					$gammacorr = 2.2;	// gamma correction
					$gamma = intval((pow((((127 - $col['alpha']) * 255 / 127) / 255), $gammacorr) * 255));
					$mimgdata .= chr($gamma);
				}
			}
		}

		if ($targetcs=='DeviceGray') { $ncols = 1; }
		else if ($targetcs=='DeviceRGB') { $ncols = 3; }
		else if ($targetcs=='DeviceCMYK') { $ncols = 4; }

		$imgdata = gzcompress($imgdata);
		$info = array('w'=>$w,'h'=>$h,'cs'=>$targetcs,'bpc'=>8,'f'=>'FlateDecode','data'=>$imgdata, 'type'=>'png',
			'parms'=>'/DecodeParms <</Colors '.$ncols.' /BitsPerComponent 8 /Columns '.$w.'>>');
		if ($dpi) { $info['set-dpi'] = $dpi; }
		if ($mask) { 
			$mimgdata = gzcompress($mimgdata); 
			$minfo = array('w'=>$w,'h'=>$h,'cs'=>'DeviceGray','bpc'=>8,'f'=>'FlateDecode','data'=>$mimgdata, 'type'=>'png',
			'parms'=>'/DecodeParms <</Colors '.$ncols.' /BitsPerComponent 8 /Columns '.$w.'>>');
			if ($dpi) { $minfo['set-dpi'] = $dpi; }
			$tempfile = '_tempImgPNG'.RAND(1,10000).'.png';
			$imgmask = count($this->images)+1;
			$minfo['i']=$imgmask ;
			$this->images[$tempfile] = $minfo;
			$info['masked'] = $imgmask;
		}
		else if ($trns) { $info['trns'] = $trns; }
		imagedestroy($im);
	}
	return $info;
}




function _fourbytes2int($s) {
	//Read a 4-byte integer from string
	return (ord($s[0])<<24) + (ord($s[1])<<16) + (ord($s[2])<<8) + ord($s[3]);
}

function _twobytes2int($s) {
	//Read a 2-byte integer from string
	return (ord(substr($s, 0, 1))<<8) + ord(substr($s, 1, 1));
}

function _jpgHeaderFromString(&$data) {
	$p = 4;
	$p += $this->_twobytes2int(substr($data, $p, 2));	// Length of initial marker block
	$marker = substr($data, $p, 2);
	while($marker != chr(255).chr(192) && $marker != chr(255).chr(194) && $p<strlen($data)) {
		// Start of frame marker (FFC0) or (FFC2) mPDF 4.4.004
		$p += ($this->_twobytes2int(substr($data, $p+2, 2))) + 2;	// Length of marker block
		$marker = substr($data, $p, 2);
	}
	if ($marker != chr(255).chr(192) && $marker != chr(255).chr(194)) { return false; }
	return substr($data, $p+2, 10);
}

function _jpgDataFromHeader($hdr) {
	$bpc = ord(substr($hdr, 2, 1));
	if (!$bpc) { $bpc = 8; }
	$h = $this->_twobytes2int(substr($hdr, 3, 2));
	$w = $this->_twobytes2int(substr($hdr, 5, 2));
	$channels = ord(substr($hdr, 7, 1));
	if ($channels==3) { $colspace='DeviceRGB'; }
	elseif($channels==4) { $colspace='DeviceCMYK'; }
	else { $colspace='DeviceGray'; }
	return array($w, $h, $colspace, $bpc);
}

function file_get_contents_by_curl($url, &$data) {
	$timeout = 5;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt ( $ch , CURLOPT_RETURNTRANSFER , 1 );
	curl_setopt ( $ch , CURLOPT_CONNECTTIMEOUT , $timeout );
	$data = curl_exec($ch);
	curl_close($ch);
}


function file_get_contents_by_socket($url, &$data) {
	$timeout = 1;
	$p = parse_url($url);
	$file = $p['path'];
	if ($p['query']) { $file .= '?'.$p['query']; }
	if(!($fh = @fsockopen($p['host'], 80, $errno, $errstr, $timeout))) { return false; }
	$getstring =
		"GET ".$file." HTTP/1.0 \r\n" .
		"Host: ".$p['host']." \r\n" .
		"Connection: close\r\n\r\n";
	fwrite($fh, $getstring);
	// Get rid of HTTP header
	$s = fgets($fh, 1024);
	if (!$s) { return false; }
	$httpheader .= $s;
	while (!feof($fh)) {
		$s = fgets($fh, 1024);
		if ( $s == "\r\n" ) { break; }
	}
	$data = '';
	while (!feof($fh)) {
		$data .= fgets($fh, 1024);
	}
	fclose($fh);
}

//==============================================================

function _imageTypeFromString(&$data) {
	$type = '';
	if (substr($data, 6, 4)== 'JFIF' || substr($data, 6, 4)== 'Exif') { 
		$type = 'jpeg'; 
	}
	else if (substr($data, 0, 6)== "GIF87a" || substr($data, 0, 6)== "GIF89a") { 
		$type = 'gif';
	}
	else if (substr($data, 0, 8)== chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) { 
		$type = 'png';
	}
/*-- IMAGES-WMF --*/
	else if (substr($data, 0, 4)== chr(215).chr(205).chr(198).chr(154)) { 
		$type = 'wmf';
	}
/*-- END IMAGES-WMF --*/
	else if (preg_match('/<svg.*<\/svg>/is',$data)) { 
		$type = 'svg';
	}
	// BMP images
	else if (substr($data, 0, 2)== "BM") { 
		$type = 'bmp';
	}
	return $type;
}
//==============================================================

// Moved outside WMF as also needed for SVG
function _putformobjects() {
	reset($this->formobjects);
	while(list($file,$info)=each($this->formobjects)) {
		$this->_newobj();
		$this->formobjects[$file]['n']=$this->n;
		$this->_out('<</Type /XObject');
		$this->_out('/Subtype /Form');
		$this->_out('/Group '.($this->n+1).' 0 R');
		$this->_out('/BBox ['.$info['x'].' '.$info['y'].' '.($info['w']+$info['x']).' '.($info['h']+$info['y']).']');
		if ($this->compress)
			$this->_out('/Filter /FlateDecode');
		$data=($this->compress) ? gzcompress($info['data']) : $info['data'];
		$this->_out('/Length '.strlen($data).'>>');
		$this->_putstream($data);
		unset($this->formobjects[$file]['data']);
		$this->_out('endobj');
		// Required for SVG transparency (opacity) to work
		$this->_newobj();
		$this->_out('<</Type /Group');
		$this->_out('/S /Transparency');
		$this->_out('>>');
		$this->_out('endobj');
	}
}

function _freadint($f)
{
	//Read a 4-byte integer from file
	$i=ord(fread($f,1))<<24;
	$i+=ord(fread($f,1))<<16;
	$i+=ord(fread($f,1))<<8;
	$i+=ord(fread($f,1));
	return $i;
}

function _UTF16BEtextstring($s) {
	$s = $this->UTF8ToUTF16BE($s, true);
/*-- ENCRYPTION --*/
	if ($this->encrypted) {
		$s = $this->_RC4($this->_objectkey($this->_current_obj_id), $s);
	}
/*-- END ENCRYPTION --*/
	return '('. $this->_escape($s).')';
}

function _textstring($s) {
/*-- ENCRYPTION --*/
	if ($this->encrypted) {
		$s = $this->_RC4($this->_objectkey($this->_current_obj_id), $s);
	}
/*-- END ENCRYPTION --*/
	return '('. $this->_escape($s).')';
}


function _escape($s)
{
	// the chr(13) substitution fixes the Bugs item #1421290.
	return strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r'));
}

function _putstream($s) {
/*-- ENCRYPTION --*/
	if ($this->encrypted) {
		$s = $this->_RC4($this->_objectkey($this->_current_obj_id), $s);
	}
/*-- END ENCRYPTION --*/
	$this->_out('stream');
	$this->_out($s);
	$this->_out('endstream');
}


function _out($s,$ln=true) {
	if($this->state==2) {
	   if ($this->bufferoutput) {
		$this->headerbuffer.= $s."\n";
	   }
/*-- COLUMNS --*/
	   else if (($this->ColActive) && !$this->processingHeader && !$this->processingFooter) {
		// Captures everything in buffer for columns; Almost everything is sent from fn. Cell() except:
		// Images sent from Image() or
		// later sent as _out($textto) in printbuffer
		// Line()
		if (preg_match('/q \d+\.\d\d+ 0 0 (\d+\.\d\d+) \d+\.\d\d+ \d+\.\d\d+ cm \/(I|FO)\d+ Do Q/',$s,$m)) {	// Image data 
			$h = ($m[1]/_MPDFK);
			// Update/overwrite the lowest bottom of printing y value for a column
			$this->ColDetails[$this->CurrCol]['bottom_margin'] = $this->y+$h;
		}
/*-- TABLES --*/
		else if (preg_match('/\d+\.\d\d+ \d+\.\d\d+ \d+\.\d\d+ ([\-]{0,1}\d+\.\d\d+) re/',$s,$m) && $this->tableLevel>0) { // Rect in table
			$h = ($m[1]/_MPDFK);
			// Update/overwrite the lowest bottom of printing y value for a column
			$this->ColDetails[$this->CurrCol]['bottom_margin'] = max($this->ColDetails[$this->CurrCol]['bottom_margin'],($this->y+$h));
		}
/*-- END TABLES --*/
		else { 	// Td Text Set in Cell()
			if (isset($this->ColDetails[$this->CurrCol]['bottom_margin'])) { $h = $this->ColDetails[$this->CurrCol]['bottom_margin'] - $this->y; }
			else { $h = 0; }
		}
		if ($h < 0) { $h = -$h; }
		$this->columnbuffer[] = array(
		's' => $s,							// Text string to output 
		'col' => $this->CurrCol, 				// Column when printed 
		'x' => $this->x, 						// x when printed 
		'y' => $this->y,					 	// this->y when printed (after column break) 
		'h' => $h						 	// actual y at bottom when printed = y+h  
		);
	   }
/*-- END COLUMNS --*/
/*-- TABLES --*/
	   else if ($this->table_rotate && !$this->processingHeader && !$this->processingFooter) {
		// Captures eveything in buffer for rotated tables; 
		$this->tablebuffer .= $s . "\n";
	   }
/*-- END TABLES --*/
	   else if ($this->kwt && !$this->processingHeader && !$this->processingFooter) {
		// Captures eveything in buffer for keep-with-table (h1-6); 
		$this->kwt_buffer[] = array(
		's' => $s,							// Text string to output 
		'x' => $this->x, 						// x when printed  
		'y' => $this->y,					 	// y when printed  
		);
	   }
	   else if (($this->keep_block_together) && !$this->processingHeader && !$this->processingFooter) {
		if (!isset($this->ktBlock[$this->page]['bottom_margin'])) {
			$this->ktBlock[$this->page]['bottom_margin'] = $this->y;
		}

		// Captures eveything in buffer; 
		if (preg_match('/q \d+\.\d\d+ 0 0 (\d+\.\d\d+) \d+\.\d\d+ \d+\.\d\d+ cm \/(I|FO)\d+ Do Q/',$s,$m)) {	// Image data 
			$h = ($m[1]/_MPDFK);
			// Update/overwrite the lowest bottom of printing y value for Keep together block
			$this->ktBlock[$this->page]['bottom_margin'] = $this->y+$h;
		}
		else { 	// Td Text Set in Cell()
			if (isset($this->ktBlock[$this->page]['bottom_margin'])) { $h = $this->ktBlock[$this->page]['bottom_margin'] - $this->y; }
			else { $h = 0; }
		}
		if ($h < 0) { $h = -$h; }
		$this->divbuffer[] = array(
		'page' => $this->page,
		's' => $s,							// Text string to output 
		'x' => $this->x, 						// x when printed 
		'y' => $this->y,					 	// y when printed (after column break)
		'h' => $h						 	// actual y at bottom when printed = y+h
		);
	   }
	   else {
		$this->pages[$this->page] .= $s.($ln == true ? "\n" : '');
	   }

	}
	else {
		$this->buffer .= $s.($ln == true ? "\n" : '');
	}
}

/*-- WATERMARK --*/
// add a watermark 
function watermark( $texte, $angle=45, $fontsize=96, $alpha=0.2 ) {
	if ($this->PDFA || $this->PDFX) { $this->Error('PDFA and PDFX do not permit transparency, so mPDF does not allow Watermarks!'); }
	if (!$this->watermark_font) { $this->watermark_font = $this->default_font; }
      $this->SetFont( $this->watermark_font, "B", $fontsize, false );	// Don't output
	$texte= $this->purify_utf8_text($texte);
	if ($this->text_input_as_HTML) {
		$texte= $this->all_entities_to_utf8($texte);
	}
	if ($this->usingCoreFont) { $texte = mb_convert_encoding($texte,$this->mb_enc,'UTF-8'); }
	// DIRECTIONALITY
	$this->magic_reverse_dir($texte, true, $this->directionality);	// *RTL*
	// Font-specific ligature substitution for Indic fonts
	if (isset($this->CurrentFont['indic']) && $this->CurrentFont['indic']) $this->ConvertIndic($texte);	// *INDIC*

	$this->SetAlpha($alpha);

	$this->SetTColor($this->ConvertColor(0));
	$szfont = $fontsize;
	$loop   = 0;
	$maxlen = (min($this->w,$this->h) );	// sets max length of text as 7/8 width/height of page
	while ( $loop == 0 )
	{
       $this->SetFont( $this->watermark_font, "B", $szfont, false );	// Don't output
	 $offset =  ((sin(deg2rad($angle))) * ($szfont/_MPDFK));

       $strlen = $this->GetStringWidth($texte);
       if ( $strlen > $maxlen - $offset  )
          $szfont --;
       else
          $loop ++;
	}

	$this->SetFont( $this->watermark_font, "B", $szfont-0.1, true, true);	// Output The -0.1 is because SetFont above is not written to PDF
											// Repeating it will not output anything as mPDF thinks it is set
	$adj = ((cos(deg2rad($angle))) * ($strlen/2));
	$opp = ((sin(deg2rad($angle))) * ($strlen/2));
	$wx = ($this->w/2) - $adj + $offset/3;
	$wy = ($this->h/2) + $opp;
	$this->Rotate($angle,$wx,$wy);
	$this->Text($wx,$wy,$texte);
	$this->Rotate(0);
	$this->SetTColor($this->ConvertColor(0));

	$this->SetAlpha(1);

}

function watermarkImg( $src, $alpha=0.2 ) {
	if ($this->PDFA || $this->PDFX) { $this->Error('PDFA and PDFX do not permit transparency, so mPDF does not allow Watermarks!'); }
	if ($this->watermarkImgBehind) { $this->watermarkImgAlpha = $this->SetAlpha($alpha, 'Normal', true); }
	else { $this->SetAlpha($alpha, $this->watermarkImgAlphaBlend); }
	$this->Image($src,0,0,0,0,'','', true, true, true);
	if (!$this->watermarkImgBehind) { $this->SetAlpha(1); }
}
/*-- END WATERMARK --*/


function Rotate($angle,$x=-1,$y=-1)
{
	if($x==-1)
		$x=$this->x;
	if($y==-1)
		$y=$this->y;
	if($this->angle!=0)
		$this->_out('Q');
	$this->angle=$angle;
	if($angle!=0)
	{
		$angle*=M_PI/180;
		$c=cos($angle);
		$s=sin($angle);
		$cx=$x*_MPDFK;
		$cy=($this->h-$y)*_MPDFK;
		$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.3F %.3F cm 1 0 0 1 %.3F %.3F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
	}
}



// mPDF 5.3.40  5.3.51  5.3.53
function CircularText($x, $y, $r, $text, $align='top', $fontfamily='', $fontsize=0, $fontstyle='', $kerning=120, $fontwidth=100) {
	if (!class_exists('directw', false)) { include(_MPDF_PATH.'classes/directw.php'); }
	if (empty($this->directw)) { $this->directw = new directw($this); }
	$this->directw->CircularText($x, $y, $r, $text, $align, $fontfamily, $fontsize, $fontstyle, $kerning, $fontwidth);
}


// From Invoice
function RoundedRect($x, $y, $w, $h, $r, $style = '')
{
	$hp = $this->h;
	if($style=='F')
		$op='f';
	elseif($style=='FD' or $style=='DF')
		$op='B';
	else
		$op='S';
	$MyArc = 4/3 * (sqrt(2) - 1);
	$this->_out(sprintf('%.3F %.3F m',($x+$r)*_MPDFK,($hp-$y)*_MPDFK ));
	$xc = $x+$w-$r ;
	$yc = $y+$r;
	$this->_out(sprintf('%.3F %.3F l', $xc*_MPDFK,($hp-$y)*_MPDFK ));

	$this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
	$xc = $x+$w-$r ;
	$yc = $y+$h-$r;
	$this->_out(sprintf('%.3F %.3F l',($x+$w)*_MPDFK,($hp-$yc)*_MPDFK));
	$this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
	$xc = $x+$r ;
	$yc = $y+$h-$r;
	$this->_out(sprintf('%.3F %.3F l',$xc*_MPDFK,($hp-($y+$h))*_MPDFK));
	$this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
	$xc = $x+$r ;
	$yc = $y+$r;
	$this->_out(sprintf('%.3F %.3F l',($x)*_MPDFK,($hp-$yc)*_MPDFK ));
	$this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
	$this->_out($op);
}

function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
{
	$h = $this->h;
	$this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F c ', $x1*_MPDFK, ($h-$y1)*_MPDFK,
						$x2*_MPDFK, ($h-$y2)*_MPDFK, $x3*_MPDFK, ($h-$y3)*_MPDFK));
}




//====================================================



/*-- DIRECTW --*/
function Shaded_box( $text,$font='',$fontstyle='B',$szfont='',$width='70%',$style='DF',$radius=2.5,$fill='#FFFFFF',$color='#000000',$pad=2 ) {
	// F (shading - no line),S (line, no shading),DF (both)
	if (!class_exists('directw', false)) { include(_MPDF_PATH.'classes/directw.php'); }
	if (empty($this->directw)) { $this->directw = new directw($this); }
	$this->directw->Shaded_box( $text,$font,$fontstyle,$szfont,$width,$style,$radius,$fill,$color,$pad);
}
/*-- END DIRECTW --*/


function UTF8StringToArray($str, $addSubset=true) {
   $out = array();
   $len = strlen($str);
   for ($i = 0; $i < $len; $i++) {
	$uni = -1;
      $h = ord($str[$i]);
      if ( $h <= 0x7F )
         $uni = $h;
      elseif ( $h >= 0xC2 ) {
         if ( ($h <= 0xDF) && ($i < $len -1) )
            $uni = ($h & 0x1F) << 6 | (ord($str[++$i]) & 0x3F);
         elseif ( ($h <= 0xEF) && ($i < $len -2) )
            $uni = ($h & 0x0F) << 12 | (ord($str[++$i]) & 0x3F) << 6 | (ord($str[++$i]) & 0x3F);
         elseif ( ($h <= 0xF4) && ($i < $len -3) )
            $uni = ($h & 0x0F) << 18 | (ord($str[++$i]) & 0x3F) << 12 | (ord($str[++$i]) & 0x3F) << 6 | (ord($str[++$i]) & 0x3F);
      }
	if ($uni >= 0) {
		$out[] = $uni;
		if ($addSubset && isset($this->CurrentFont['subset'])) {
			$this->CurrentFont['subset'][$uni] = $uni;
		}
	}
   }
   return $out;
}


//Convert utf-8 string to <HHHHHH> for Font Subsets
function UTF8toSubset($str) {
	$ret = '<';
	$str = preg_replace('/'.preg_quote($this->aliasNbPg,'/').'/', chr(7), $str );
	$str = preg_replace('/'.preg_quote($this->aliasNbPgGp,'/').'/', chr(8), $str );
	$unicode = $this->UTF8StringToArray($str);
	$orig_fid = $this->CurrentFont['subsetfontids'][0];
	$last_fid = $this->CurrentFont['subsetfontids'][0];
	foreach($unicode as $c) {
	   if ($c == 7 || $c == 8) { 
			if ($orig_fid != $last_fid) {
				$ret .= '> Tj /F'.$orig_fid.' '.$this->FontSizePt.' Tf <';
				$last_fid = $orig_fid;
			}
			if ($c == 7) { $ret .= $this->aliasNbPgHex; }
			else { $ret .= $this->aliasNbPgGpHex; }
			continue;
	   }
	   for ($i=0; $i<99; $i++) {
		// return c as decimal char
		$init = array_search($c, $this->CurrentFont['subsets'][$i]);
		if ($init!==false) {
			if ($this->CurrentFont['subsetfontids'][$i] != $last_fid) {
				$ret .= '> Tj /F'.$this->CurrentFont['subsetfontids'][$i].' '.$this->FontSizePt.' Tf <';
				$last_fid = $this->CurrentFont['subsetfontids'][$i];
			}
			$ret .= sprintf("%02s", strtoupper(dechex($init)));
			break;
		}
		// TrueType embedded SUBSETS
		else if (count($this->CurrentFont['subsets'][$i]) < 255) {
			$n = count($this->CurrentFont['subsets'][$i]);
			$this->CurrentFont['subsets'][$i][$n] = $c;
			if ($this->CurrentFont['subsetfontids'][$i] != $last_fid) {
				$ret .= '> Tj /F'.$this->CurrentFont['subsetfontids'][$i].' '.$this->FontSizePt.' Tf <';
				$last_fid = $this->CurrentFont['subsetfontids'][$i];
			}
			$ret .= sprintf("%02s", strtoupper(dechex($n)));
			break;
		}
		else if (!isset($this->CurrentFont['subsets'][($i+1)])) {
			// TrueType embedded SUBSETS
			$this->CurrentFont['subsets'][($i+1)] = array(0=>0);
			$new_fid = count($this->fonts)+$this->extraFontSubsets+1;
			$this->CurrentFont['subsetfontids'][($i+1)] = $new_fid;
			$this->extraFontSubsets++;
		}
	   }
	}
	$ret .= '>';
	if ($last_fid != $orig_fid) {
		$ret .= ' Tj /F'.$orig_fid.' '.$this->FontSizePt.' Tf <> ';
	}
	return $ret;
}


// Converts UTF-8 strings to UTF16-BE.
function UTF8ToUTF16BE($str, $setbom=true) {
	if ($this->checkSIP && preg_match("/([\x{20000}-\x{2FFFF}])/u", $str)) { 
	   if (!in_array($this->currentfontfamily, array('gb','big5','sjis','uhc','gbB','big5B','sjisB','uhcB','gbI','big5I','sjisI','uhcI',
		'gbBI','big5BI','sjisBI','uhcBI'))) {
		$str = preg_replace("/[\x{20000}-\x{2FFFF}]/u", chr(0), $str);
	   }
	}
	if ($this->checkSMP && preg_match("/([\x{10000}-\x{1FFFF}])/u", $str )) { 
		$str = preg_replace("/[\x{10000}-\x{1FFFF}]/u", chr(0), $str );
	}
	$outstr = ""; // string to be returned
	if ($setbom) {
		$outstr .= "\xFE\xFF"; // Byte Order Mark (BOM)
	}
	$outstr .= mb_convert_encoding($str, 'UTF-16BE', 'UTF-8');
	return $outstr;
}





// ====================================================
// ====================================================
/*-- CJK-FONTS --*/

// from class PDF_Chinese CJK EXTENSIONS
function AddCIDFont($family,$style,$name,&$cw,$CMap,$registry,$desc)
{
	$fontkey=strtolower($family).strtoupper($style);
	if(isset($this->fonts[$fontkey]))
		$this->Error("Font already added: $family $style");
	$i=count($this->fonts)+$this->extraFontSubsets+1;
	$name=str_replace(' ','',$name);
	if ($family == 'sjis') { $up = -120; } else { $up = -130; }
	// ? 'up' and 'ut' do not seem to be referenced anywhere
	$this->fonts[$fontkey]=array('i'=>$i,'type'=>'Type0','name'=>$name,'up'=>$up,'ut'=>40,'cw'=>$cw,'CMap'=>$CMap,'registry'=>$registry,'MissingWidth'=>1000,'desc'=>$desc);
}

function AddCJKFont($family) {

	if ($this->PDFA || $this->PDFX) {
		$this->Error("Adobe CJK fonts cannot be embedded in mPDF (required for PDFA1-b and PDFX/1-a).");
	}
	if ($family == 'big5') { $this->AddBig5Font(); }
	else if ($family == 'gb') { $this->AddGBFont(); }
	else if ($family == 'sjis') { $this->AddSJISFont(); }
	else if ($family == 'uhc') { $this->AddUHCFont(); }
}

function AddBig5Font()
{
	//Add Big5 font with proportional Latin
	$family='big5';
	$name='MSungStd-Light-Acro';
	$cw=$this->Big5_widths;
	$CMap='UniCNS-UTF16-H';
	$registry=array('ordering'=>'CNS1','supplement'=>4);
	$desc = array(
	'Ascent' => 880,
	'Descent' => -120,
	'CapHeight' => 880,
	'Flags' => 6,
	'FontBBox' => '[-160 -249 1015 1071]',
	'ItalicAngle' => 0,
	'StemV' => 93,
	);
	$this->AddCIDFont($family,'',$name,$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'B',$name.',Bold',$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'I',$name.',Italic',$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'BI',$name.',BoldItalic',$cw,$CMap,$registry,$desc);
}


function AddGBFont()
{
	//Add GB font with proportional Latin
	$family='gb';
	$name='STSongStd-Light-Acro';	
	$cw=$this->GB_widths;
	$CMap='UniGB-UTF16-H';
	$registry=array('ordering'=>'GB1','supplement'=>4);
	$desc = array(
	'Ascent' => 752,
	'Descent' => -271,
	'CapHeight' => 737,
	'Flags' => 6,
	'FontBBox' => '[-25 -254 1000 880]',
	'ItalicAngle' => 0,
	'StemV' => 58,
	'Style' => '<< /Panose <000000000400000000000000> >>',
	);
	$this->AddCIDFont($family,'',$name,$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'B',$name.',Bold',$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'I',$name.',Italic',$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'BI',$name.',BoldItalic',$cw,$CMap,$registry,$desc);
}


function AddSJISFont()
{
	//Add SJIS font with proportional Latin
	$family='sjis';
	$name='KozMinPro-Regular-Acro';
	$cw=$this->SJIS_widths;
	$CMap='UniJIS-UTF16-H';
	$registry=array('ordering'=>'Japan1','supplement'=>5);
	$desc = array(
	'Ascent' => 880,
	'Descent' => -120,
	'CapHeight' => 740,
	'Flags' => 6,
	'FontBBox' => '[-195 -272 1110 1075]',
	'ItalicAngle' => 0,
	'StemV' => 86,
	'XHeight' => 502,
	);
	$this->AddCIDFont($family,'',$name,$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'B',$name.',Bold',$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'I',$name.',Italic',$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'BI',$name.',BoldItalic',$cw,$CMap,$registry,$desc);
}

function AddUHCFont()
{
	//Add UHC font with proportional Latin
	$family='uhc';
	$name='HYSMyeongJoStd-Medium-Acro';	
	$cw=$this->UHC_widths;
	$CMap='UniKS-UTF16-H';
	$registry=array('ordering'=>'Korea1','supplement'=>2);
	$desc = array(
	'Ascent' => 880,
	'Descent' => -120,
	'CapHeight' => 720,
	'Flags' => 6,
	'FontBBox' => '[-28 -148 1001 880]',
	'ItalicAngle' => 0,
	'StemV' => 60,
	'Style' => '<< /Panose <000000000600000000000000> >>',
	);
	$this->AddCIDFont($family,'',$name,$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'B',$name.',Bold',$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'I',$name.',Italic',$cw,$CMap,$registry,$desc);
	$this->AddCIDFont($family,'BI',$name.',BoldItalic',$cw,$CMap,$registry,$desc);
}

/*-- END CJK-FONTS --*/

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
function SetAutoFont($af = AUTOFONT_ALL) {
	if ($this->onlyCoreFonts) { return false; }
	if (!$af && $af !== 0) { $af = AUTOFONT_ALL; }
	$this->autoFontGroups = $af;
	if ($this->autoFontGroups ) { 
		$this->useLang = true;
	}
}


function SetDefaultFont($font) {
	// Disallow embedded fonts to be used as defaults in PDFA
	if ($this->PDFA || $this->PDFX) {
		if (strtolower($font) == 'ctimes') { $font = 'serif'; }
		if (strtolower($font) == 'ccourier') { $font = 'monospace'; }
		if (strtolower($font) == 'chelvetica') { $font = 'sans-serif'; }
	}
  	$font = $this->SetFont($font);	// returns substituted font if necessary
	$this->default_font = $font;
	$this->original_default_font = $font;
	if (!$this->watermark_font ) { $this->watermark_font = $font; }	// *WATERMARK*
	$this->defaultCSS['BODY']['FONT-FAMILY'] = $font;
	$this->CSS['BODY']['FONT-FAMILY'] = $font;
}

function SetDefaultFontSize($fontsize) {
	$this->default_font_size = $fontsize;
	$this->original_default_font_size = $fontsize;
	$this->SetFontSize($fontsize);
	$this->defaultCSS['BODY']['FONT-SIZE'] = $fontsize . 'pt';
	$this->CSS['BODY']['FONT-SIZE'] = $fontsize . 'pt';
}

function SetDefaultBodyCSS($prop, $val) {
   if ($prop) {
	$this->defaultCSS['BODY'][strtoupper($prop)] = $val;
	$this->CSS['BODY'][strtoupper($prop)] = $val;
  }
}


function SetDirectionality($dir='ltr') {
/*-- RTL --*/
	if (strtolower($dir) == 'rtl') { 
	  if ($this->directionality != 'rtl') {
		// Swop L/R Margins so page 1 RTL is an 'even' page
		$tmp = $this->DeflMargin;
		$this->DeflMargin = $this->DefrMargin; 
		$this->DefrMargin = $tmp; 
		$this->orig_lMargin = $this->DeflMargin;
		$this->orig_rMargin = $this->DefrMargin;

		$this->SetMargins($this->DeflMargin,$this->DefrMargin,$this->tMargin);
	  }
		$this->directionality = 'rtl'; 
		$this->defaultAlign = 'R';
		$this->defaultTableAlign = 'R';
	}
	else  { 
/*-- END RTL --*/
		$this->directionality = 'ltr'; 
		$this->defaultAlign = 'L';
		$this->defaultTableAlign = 'L';
	}	// *RTL*
	$this->CSS['BODY']['DIRECTION'] = $this->directionality;
}



// Added to set line-height-correction
function SetLineHeightCorrection($val) {
	if ($val > 0) { $this->default_lineheight_correction = $val; }
	else { $this->default_lineheight_correction = 1.2; }
}

// Set a (fixed) lineheight to an actual value - either to named fontsize(pts) or default
function SetLineHeight($FontPt='',$spacing = '') {
   if ($this->shrin_k > 1) { $k = $this->shrin_k; }
   else { $k = 1; }
   if ($spacing > 0) { 
	if (preg_match('/mm/',$spacing)) { 
		$this->lineheight = ($spacing + 0.0) / $k; // convert to number
	}
	else  { 
		if ($FontPt) { $this->lineheight = (($FontPt/_MPDFK) *$spacing); }
		else { $this->lineheight = (($this->FontSizePt/_MPDFK) *$spacing); }
	}
   }
   else {
	if ($FontPt) { $this->lineheight = (($FontPt/_MPDFK) *$this->normalLineheight); }
	else { $this->lineheight = (($this->FontSizePt/_MPDFK) *$this->normalLineheight); }
   }
}

function _computeLineheight($lh, $fs='') {
	if ($this->shrin_k > 1) { $k = $this->shrin_k; }
	else { $k = 1; }
	if (!$fs) { $fs = $this->FontSize; }
	if (preg_match('/mm/',$lh)) { 
		return (($lh + 0.0) / $k); // convert to number
	}
	else if ($lh > 0) { 
		return ($fs * $lh);
	}
	else if (isset($this->normalLineheight)) { return ($fs * $this->normalLineheight); }
	else return ($fs * $this->default_lineheight_correction); 
}


function SetBasePath($str='') {
  if ( isset($_SERVER['HTTP_HOST']) ) { $host = $_SERVER['HTTP_HOST']; }
  else if ( isset($_SERVER['SERVER_NAME']) ) { $host = $_SERVER['SERVER_NAME']; }
  else { $host = ''; }
  if (!$str) { 
	if ($_SERVER['SCRIPT_NAME']) { $currentPath = dirname($_SERVER['SCRIPT_NAME']); }
	else { $currentPath = dirname($_SERVER['PHP_SELF']); }
	$currentPath = str_replace("\\","/",$currentPath);
	if ($currentPath == '/') { $currentPath = ''; }
	if ($host) { $currpath = 'http://' . $host . $currentPath .'/'; }
	else { $currpath = ''; }
	$this->basepath = $currpath; 
	$this->basepathIsLocal = true; 
	return; 
  }
  $str = preg_replace('/\?.*/','',$str);
  if (!preg_match('/(http|https|ftp):\/\/.*\//i',$str)) { $str .= '/'; } 
  $str .= 'xxx';	// in case $str ends in / e.g. http://www.bbc.co.uk/
  $this->basepath = dirname($str) . "/";	// returns e.g. e.g. http://www.google.com/dir1/dir2/dir3/
  $this->basepath = str_replace("\\","/",$this->basepath); //If on Windows
  $tr = parse_url($this->basepath);
  if (isset($tr['host']) && ($tr['host'] == $host)) { $this->basepathIsLocal = true; }
  else { $this->basepathIsLocal = false; }
}


function GetFullPath(&$path,$basepath='') {
	// When parsing CSS need to pass temporary basepath - so links are relative to current stylesheet
	if (!$basepath) { $basepath = $this->basepath; }
	//Fix path value
	$path = str_replace("\\","/",$path); //If on Windows
	//Get link info and obtain its absolute path
	$regexp = '|^./|';	// Inadvertently corrects "./path/etc" and "//www.domain.com/etc"
	$path = preg_replace($regexp,'',$path);

	if(substr($path,0,1) == '#') { return; }
	if (stristr($path,"mailto:") !== false) { return; }
	if (strpos($path,"../") !== false ) { //It is a Relative Link
		$backtrackamount = substr_count($path,"../");
		$maxbacktrack = substr_count($basepath,"/") - 1;
		$filepath = str_replace("../",'',$path);
		$path = $basepath;
		//If it is an invalid relative link, then make it go to directory root
		if ($backtrackamount > $maxbacktrack) $backtrackamount = $maxbacktrack;
		//Backtrack some directories
		for( $i = 0 ; $i < $backtrackamount + 1 ; $i++ ) $path = substr( $path, 0 , strrpos($path,"/") );
		$path = $path . "/" . $filepath; //Make it an absolute path
	}
	else if( strpos($path,":/") === false || strpos($path,":/") > 10) { //It is a Local Link
		if (substr($path,0,1) == "/") { 
			$tr = parse_url($basepath);
			$root = $tr['scheme'].'://'.$tr['host'];
			$path = $root . $path; 
		}
		else { $path = $basepath . $path; }
	}
	//Do nothing if it is an Absolute Link
}


// Used for external CSS files
function _get_file($path) {
	// If local file try using local path (? quicker, but also allowed even if allow_url_fopen false)
	$contents = '';
	$contents = @file_get_contents($path);
	if ($contents) { return $contents; }
	if ($this->basepathIsLocal) {
		$tr = parse_url($path);
		$lp=getenv("SCRIPT_NAME");
		$ap=realpath($lp);
		$ap=str_replace("\\","/",$ap);
		$docroot=substr($ap,0,strpos($ap,$lp));
		// WriteHTML parses all paths to full URLs; may be local file name 
		if ($tr['scheme'] && $tr['host'] && $_SERVER["DOCUMENT_ROOT"] ) { 
			$localpath = $_SERVER["DOCUMENT_ROOT"] . $tr['path']; 
		}
		// DOCUMENT_ROOT is not returned on IIS
		else if ($docroot) {
			$localpath = $docroot . $tr['path'];
		}
		else { $localpath = $path; }
		$contents = @file_get_contents($localpath);
	}
	// if not use full URL
	else if (!$contents && !ini_get('allow_url_fopen') && function_exists("curl_init"))  {
		$ch = curl_init($path);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt ( $ch , CURLOPT_RETURNTRANSFER , 1 );
		$contents = curl_exec($ch);
		curl_close($ch);
	}
	return $contents;
}


function docPageNum($num = 0, $extras = false) {
	if ($num < 1) { $num = $this->page; }
	$type = '1';	// set default decimal
	$ppgno = $num;
	$suppress = 0;
	$offset = 0;

	$lastreset = 0;
	foreach($this->PageNumSubstitutions AS $psarr) {
		if ($num >= $psarr['from']) {
			if ($psarr['reset']) { 
				if ($psarr['reset']>1) { $offset = $psarr['reset']-1; }
				$ppgno = $num - $psarr['from'] + 1 + $offset; 
				$lastreset = $psarr['from'];
			}
			if ($psarr['type']) { $type = $psarr['type']; }
			if (strtoupper($psarr['suppress'])=='ON' || $psarr['suppress']==1) { $suppress = 1; }
			else if (strtoupper($psarr['suppress'])=='OFF') { $suppress = 0; }
		}
	}
	if ($suppress) { return ''; }

	foreach($this->pgsIns AS $k=>$v) {
		if ($k>$lastreset && $k<$num) {
			$ppgno -= $v;
		}
	}
	if ($type=='A') { $ppgno = $this->dec2alpha($ppgno,true); }
	else if ($type=='a') { $ppgno = $this->dec2alpha($ppgno,false);}
	else if ($type=='I') { $ppgno = $this->dec2roman($ppgno,true); }
	else if ($type=='i') { $ppgno = $this->dec2roman($ppgno,false); }
	if ($extras) { $ppgno = $this->pagenumPrefix . $ppgno . $this->pagenumSuffix; }
	return $ppgno;
}


function docPageSettings($num = 0) {
	// Returns current type (numberstyle), suppression state for this page number; 
	// reset is only returned if set for this page number
	if ($num < 1) { $num = $this->page; }
	$type = '1';	// set default decimal
	$ppgno = $num;
	$suppress = 0;
	$offset = 0;
	$reset = '';
	foreach($this->PageNumSubstitutions AS $psarr) {
		if ($num >= $psarr['from']) {
			if ($psarr['reset']) { 
				if ($psarr['reset']>1) { $offset = $psarr['reset']-1; }
				$ppgno = $num - $psarr['from'] + 1 + $offset; 
			}
			if ($psarr['type']) { $type = $psarr['type']; }
			if (strtoupper($psarr['suppress'])=='ON' || $psarr['suppress']==1) { $suppress = 1; }
			else if (strtoupper($psarr['suppress'])=='OFF') { $suppress = 0; }
		}
		if ($num == $psarr['from']) { $reset = $psarr['reset']; }
	}
	if ($suppress) { $suppress = 'on'; }
	else { $suppress = 'off'; }
	return array($type, $suppress, $reset);
}

function docPageNumTotal($num = 0, $extras = false) {
	if ($num < 1) { $num = $this->page; }
	$type = '1';	// set default decimal
	$ppgstart = 1;
	$ppgend = count($this->pages)+1; 
	$suppress = 0;
	$offset = 0;
	foreach($this->PageNumSubstitutions AS $psarr) {
		if ($num >= $psarr['from']) {
			if ($psarr['reset']) { 
				if ($psarr['reset']>1) { $offset = $psarr['reset']-1; }
				$ppgstart = $psarr['from'] + $offset; 
				$ppgend = count($this->pages)+1 + $offset; 
			}
			if ($psarr['type']) { $type = $psarr['type']; }
			if (strtoupper($psarr['suppress'])=='ON' || $psarr['suppress']==1) { $suppress = 1; }
			else if (strtoupper($psarr['suppress'])=='OFF') { $suppress = 0; }
		}
		if ($num < $psarr['from']) {
			if ($psarr['reset']) { 
				$ppgend = $psarr['from'] + $offset; 
				break;
			}
		}
	}
	if ($suppress) { return ''; }
	$ppgno = $ppgend-$ppgstart+$offset; 
	if ($extras) { $ppgno = $this->nbpgPrefix . $ppgno . $this->nbpgSuffix; }
	return $ppgno;
}

function RestartDocTemplate() {
	$this->docTemplateStart = $this->page;
}



//Page header
function Header($content='') {

	$this->cMarginL = 0;
	$this->cMarginR = 0;


/*-- HTMLHEADERS-FOOTERS --*/
  if (($this->mirrorMargins && ($this->page%2==0) && $this->HTMLHeaderE) || ($this->mirrorMargins && ($this->page%2==1) && $this->HTMLHeader) || (!$this->mirrorMargins && $this->HTMLHeader)) {
	$this->writeHTMLHeaders(); 
	return;
  }
/*-- END HTMLHEADERS-FOOTERS --*/
  $this->processingHeader=true;
  $h = $this->headerDetails;
  if(count($h)) {

	if ($this->forcePortraitHeaders && $this->CurOrientation=='L' && $this->CurOrientation!=$this->DefOrientation) {
		$this->_out(sprintf('q 0 -1 1 0 0 %.3F cm ',($this->h*_MPDFK)));
		$yadj = $this->w - $this->h;
		$headerpgwidth = $this->h - $this->orig_lMargin - $this->orig_rMargin;
		if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
			$headerlmargin = $this->orig_rMargin;
		}
		else {
			$headerlmargin = $this->orig_lMargin;
		}
	}
	else { 
		$yadj = 0; 
		$headerpgwidth = $this->pgwidth;
		$headerlmargin = $this->lMargin;
	}

	$this->y = $this->margin_header - $yadj ;
	$this->SetTColor($this->ConvertColor(0));
    	$this->SUP = false;
	$this->SUB = false;
	$this->bullet = false;

	// only show pagenumber if numbering on
	$pgno = $this->docPageNum($this->page, true); 

	if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
			$side = 'even';
	}
	else {	// ODD	// OR NOT MIRRORING MARGINS/FOOTERS = DEFAULT
			$side = 'odd';
	}
	$maxfontheight = 0;
	foreach(array('L','C','R') AS $pos) {
	  if (isset($h[$side][$pos]['content']) && $h[$side][$pos]['content']) {
		if (isset($h[$side][$pos]['font-size']) && $h[$side][$pos]['font-size']) { $hfsz = $h[$side][$pos]['font-size']; }
		else { $hfsz = $this->default_font_size; }
		$maxfontheight = max($maxfontheight,$hfsz);
	  }
	}
	// LEFT-CENTER-RIGHT
	foreach(array('L','C','R') AS $pos) {
	  if (isset($h[$side][$pos]['content']) && $h[$side][$pos]['content']) {
		$hd = str_replace('{PAGENO}',$pgno,$h[$side][$pos]['content']);
		$hd = str_replace($this->aliasNbPgGp,$this->nbpgPrefix.$this->aliasNbPgGp.$this->nbpgSuffix,$hd);
		$hd = preg_replace('/\{DATE\s+(.*?)\}/e',"date('\\1')",$hd);
		if (isset($h[$side][$pos]['font-family']) && $h[$side][$pos]['font-family']) { $hff = $h[$side][$pos]['font-family']; }
		else { $hff = $this->original_default_font; }
		if (isset($h[$side][$pos]['font-size']) && $h[$side][$pos]['font-size']) { $hfsz = $h[$side][$pos]['font-size']; }
		else { $hfsz = $this->original_default_font_size; }	// pts
		$maxfontheight = max($maxfontheight,$hfsz);
		$hfst = '';
		if (isset($h[$side][$pos]['font-style']) && $h[$side][$pos]['font-style']) { 
			$hfst = $h[$side][$pos]['font-style'];
		}
		if (isset($h[$side][$pos]['color']) && $h[$side][$pos]['color']) { 
			$hfcol = $h[$side][$pos]['color']; 
			$cor = $this->ConvertColor($hfcol);
			if ($cor) { $this->SetTColor($cor); }
		}
		else { $hfcol = ''; }
		$this->SetFont($hff,$hfst,$hfsz,true,true);
		$this->x = $headerlmargin ;
		$this->y = $this->margin_header - $yadj ;

		$hd = $this->purify_utf8_text($hd);
		if ($this->text_input_as_HTML) {
			$hd = $this->all_entities_to_utf8($hd);
		}
		// CONVERT CODEPAGE
		if ($this->usingCoreFont) { $hd = mb_convert_encoding($hd,$this->mb_enc,'UTF-8'); }
		// DIRECTIONALITY RTL
		$this->magic_reverse_dir($hd, true, $this->directionality);	// *RTL*
		// Font-specific ligature substitution for Indic fonts
		if (isset($this->CurrentFont['indic']) && $this->CurrentFont['indic']) $this->ConvertIndic($hd);	// *INDIC*
		$align = $pos;
/*-- RTL --*/
		if ($this->directionality == 'rtl') { 
			if ($pos == 'L') { $align = 'R'; }
			else if ($pos == 'R') { $align = 'L'; }
		}
/*-- END RTL --*/
		if ($pos!='L' && (strpos($hd,$this->aliasNbPg)!==false || strpos($hd,$this->aliasNbPgGp)!==false)) { 
			if (strpos($hd,$this->aliasNbPgGp)!==false) { $type= 'nbpggp'; } else { $type= 'nbpg'; }
			$this->_out('{mpdfheader'.$type.' '.$pos.' ff='.$hff.' fs='.$hfst.' fz='.$hfsz.'}'); 
			$this->Cell($headerpgwidth ,$maxfontheight/_MPDFK ,$hd,0,0,$align,0,'',0,0,0,'M');
			$this->_out('Q');
		}
		else { 
			$this->Cell($headerpgwidth ,$maxfontheight/_MPDFK ,$hd,0,0,$align,0,'',0,0,0,'M');
		}
		if ($hfcol) { $this->SetTColor($this->ConvertColor(0)); }
	  }
	}
	//Return Font to normal
	$this->SetFont($this->default_font,'',$this->original_default_font_size);
	// LINE
	if (isset($h[$side]['line']) && $h[$side]['line']) { 
		$this->SetLineWidth(0.1);
		$this->SetDColor($this->ConvertColor(0));
		$this->Line($headerlmargin , $this->margin_header + ($maxfontheight*(1+$this->header_line_spacing)/_MPDFK) - $yadj , $headerlmargin + $headerpgwidth, $this->margin_header + ($maxfontheight*(1+$this->header_line_spacing)/_MPDFK) - $yadj  );
	}
	if ($this->forcePortraitHeaders && $this->CurOrientation=='L' && $this->CurOrientation!=$this->DefOrientation) {
		$this->_out('Q');
	}
  }
  $this->SetY($this->tMargin);
  if ($this->ColActive) { $this->pgwidth = $this->ColWidth; }	// *COLUMNS*

  $this->processingHeader=false;
}



/*-- TABLES --*/
function TableHeaderFooter($content='',$tablestartpage='',$tablestartcolumn ='',$horf = 'H',$level, $firstSpread=true, $finalSpread=true) {	// mPDF 5.3.36
  if(($horf=='H' || $horf=='F') && !empty($content)) {	// mPDF 5.3.62
	$table = &$this->table[1][1];
	// Advance down page by half width of top border

	if ($horf=='H') { // Only if header
		if ($table['borders_separate']) { $adv = $table['border_spacing_V']/2 + $table['border_details']['T']['w'] + $table['padding']['T'];  }
		else { $adv = $table['max_cell_border_width']['T'] /2 ; }
		if ($adv) { 
		   if ($this->table_rotate) {
			$this->y += ($adv);
		   }
		   else {
			$this->DivLn($adv,$this->blklvl,true); 
		   }
		}
	}

   if ($horf=='F') { // Table Footer
	$firstrow = count($table['cells']) - $table['footernrows'];	// mPDF 5.3.62
	$lastrow = count($table['cells']) - 1;
   }
   else { 	// Table Header
	$firstrow = 0;
	$lastrow = $table['headernrows'] - 1;	// mPDF 5.3.62
   }

   $topy = $content[$firstrow][0]['y']-$this->y;

   for ($i=$firstrow ; $i<=$lastrow; $i++) {

    $y = $this->y;

/*-- COLUMNS --*/
	// If outside columns, this is done in PaintDivBB
	if ($this->ColActive) {
	//OUTER FILL BGCOLOR of DIVS
	 if ($this->blklvl > 0) {
	  $firstblockfill = $this->GetFirstBlockFill();
	  if ($firstblockfill && $this->blklvl >= $firstblockfill) {
		$divh = $content[$i][0]['h'];
		$bak_x = $this->x;
		$this->DivLn($divh,-3,false);
		// Reset current block fill
		$bcor = $this->blk[$this->blklvl]['bgcolorarray'];
		$this->SetFColor($bcor);
		$this->x = $bak_x;
	  }
	 }
	}
/*-- END COLUMNS --*/

    $colctr = 0;
    foreach($content[$i] as $tablehf) {
	$colctr++;
	$y = $tablehf['y'] - $topy;
      $this->y = $y;
      //Set some cell values
      $x = $tablehf['x'];
	if (($this->mirrorMargins) && ($tablestartpage == 'ODD') && (($this->page)%2==0)) {	// EVEN
		$x = $x +$this->MarginCorrection;
	}
	else if (($this->mirrorMargins) && ($tablestartpage == 'EVEN') && (($this->page)%2==1)) {	// ODD
		$x = $x +$this->MarginCorrection;
	}
/*-- COLUMNS --*/
	// Added to correct for Columns
	if ($this->ColActive) {
	   if ($this->directionality == 'rtl') {	// *RTL*
		$x -= ($this->CurrCol - $tablestartcolumn) * ($this->ColWidth+$this->ColGap);	// *RTL*
	   }	// *RTL*
	   else {	// *RTL*
		$x += ($this->CurrCol - $tablestartcolumn) * ($this->ColWidth+$this->ColGap);
	   }	// *RTL*
	}
/*-- END COLUMNS --*/

	if ($colctr==1) { $x0 = $x; }

	// mPDF ITERATION
	if ($this->iterationCounter) {
	   foreach($tablehf['textbuffer'] AS $k=>$t) {	// mPDF 5.3.78
		if (preg_match('/{iteration ([a-zA-Z0-9_]+)}/',$t, $m)) {
			$vname = '__'.$m[1].'_';
			if (!isset($this->$vname)) { $this->$vname = 1; }
			else { $this->$vname++; }
			$tablehf['textbuffer'][$k][0] = preg_replace('/{iteration '.$m[1].'}/', $this->$vname, $tablehf['textbuffer'][$k][0]);
		}
	   }
	}


      $w = $tablehf['w'];
      $h = $tablehf['h'];
      $va = $tablehf['va'];
      $R = $tablehf['R'];
      $mih = $tablehf['mih'];
      $border = $tablehf['border'];
      $border_details = $tablehf['border_details'];
      $padding = $tablehf['padding'];
	$this->tabletheadjustfinished = true;

      $textbuffer = $tablehf['textbuffer'];

      $align = $tablehf['a'];
      //Align
      $this->divalign=$align;
	$this->x = $x;

	if ($this->ColActive) {
		if ($table['borders_separate']) { 
		 $tablefill = isset($table['bgcolor'][-1]) ? $table['bgcolor'][-1] : 0;
		 if ($tablefill) {
  				$color = $this->ConvertColor($tablefill);
  				if ($color) {
					$xadj = ($table['border_spacing_H']/2);
					$yadj = ($table['border_spacing_V']/2);
					$wadj = $table['border_spacing_H'];
					$hadj = $table['border_spacing_V'];
 			   		if ($i == $firstrow && $horf=='H') {		// Top
						$yadj += $table['padding']['T'] + $table['border_details']['T']['w'] ;
						$hadj += $table['padding']['T'] + $table['border_details']['T']['w'] ;
			   		}
			   		if (($i == ($lastrow) || (isset($tablehf['rowspan']) && ($i+$tablehf['rowspan']) == ($lastrow+1))  || (!isset($tablehf['rowspan']) && ($i+1) == ($lastrow+1))) && $horf=='F') {	// Bottom
						$hadj += $table['padding']['B'] + $table['border_details']['B']['w'] ;
			   		}
			   		if ($colctr == 1) {		// Left
						$xadj += $table['padding']['L'] + $table['border_details']['L']['w'] ;
						$wadj += $table['padding']['L'] + $table['border_details']['L']['w'] ;
			   		}
			   		if ($colctr == count($content[$i]) ) {	// Right
						$wadj += $table['padding']['R'] + $table['border_details']['R']['w'] ;
			   		}
					$this->SetFColor($color);
					$this->Rect($x - $xadj, $y - $yadj, $w + $wadj, $h + $hadj, 'F');
				}
		 }
		}
	}

	if ($table['empty_cells']!='hide' || !empty($textbuffer) || !$table['borders_separate']) { $paintcell = true; }
	else { $paintcell = false; } 

	//Vertical align
	if ($R && INTVAL($R) > 0 && isset($va) && $va!='B') { $va='B';}

	if (!isset($va) || empty($va) || $va=='M') $this->y += ($h-$mih)/2;
      elseif (isset($va) && $va=='B') $this->y += $h-$mih;


	//TABLE ROW OR CELL FILL BGCOLOR
	$fill = 0;
	if (isset($tablehf['bgcolor']) && $tablehf['bgcolor'] && $tablehf['bgcolor']!='transparent') { 
		$fill = $tablehf['bgcolor'];
		$leveladj = 6;
	}
	else if (isset($content[$i][0]['trbgcolor']) && $content[$i][0]['trbgcolor'] && $content[$i][0]['trbgcolor']!='transparent') { // Row color
		$fill = $content[$i][0]['trbgcolor'];
		$leveladj = 3;
	}
	if ($fill && $paintcell) {
  		$color = $this->ConvertColor($fill);
  		if ($color) {
 			if ($table['borders_separate']) { 
			   if ($this->ColActive) {
				$this->SetFColor($color);
				$this->Rect($x+ ($table['border_spacing_H']/2), $y+ ($table['border_spacing_V']/2), $w- $table['border_spacing_H'], $h- $table['border_spacing_V'], 'F');
			   }
			   else {
				$this->tableBackgrounds[$level*9+$leveladj][] = array('gradient'=>false, 'x'=>($x + ($table['border_spacing_H']/2)), 'y'=>($y + ($table['border_spacing_V']/2)), 'w'=>($w - $table['border_spacing_H']), 'h'=>($h - $table['border_spacing_V']), 'col'=>$color);
			   }
			}
 			else { 
			   if ($this->ColActive) {
				$this->SetFColor($color);
				$this->Rect($x, $y, $w, $h, 'F');
			   }
			   else {
				$this->tableBackgrounds[$level*9+$leveladj][] = array('gradient'=>false, 'x'=>$x, 'y'=>$y, 'w'=>$w, 'h'=>$h, 'col'=>$color);
			   }
			}
		}
	}


/*-- BACKGROUNDS --*/
	if (isset($tablehf['gradient']) && $tablehf['gradient'] && $paintcell){
		$g = $this->grad->parseBackgroundGradient($tablehf['gradient']);
		if ($g) {
 		  if ($table['borders_separate']) { 
 			$px = $x+ ($table['border_spacing_H']/2);
			$py = $y+ ($table['border_spacing_V']/2);
			$pw = $w- $table['border_spacing_H'];
			$ph = $h- $table['border_spacing_V'];
		  }
		  else {
			$px = $x;
			$py = $y;
			$pw = $w;
			$ph = $h;
		  }
		  if ($this->ColActive) {
			$this->grad->Gradient($px, $py, $pw, $ph, $g['type'], $g['stops'], $g['colorspace'], $g['coords'], $g['extend']);
		  }
		  else {
			$this->tableBackgrounds[$level*9+7][] = array('gradient'=>true, 'x'=>$px, 'y'=>$py, 'w'=>$pw, 'h'=>$ph, 'gradtype'=>$g['type'], 'stops'=>$g['stops'], 'colorspace'=>$g['colorspace'], 'coords'=>$g['coords'], 'extend'=>$g['extend'], 'clippath'=>'');
		  }
		}
	}

	if (isset($tablehf['background-image']) && $paintcell){
	  if ($tablehf['background-image']['gradient'] && preg_match('/(-moz-)*(repeating-)*(linear|radial)-gradient/', $tablehf['background-image']['gradient'] )) {
		$g = $this->grad->parseMozGradient( $tablehf['background-image']['gradient'] );
		if ($g) {
 		  if ($table['borders_separate']) { 
 			$px = $x+ ($table['border_spacing_H']/2);
			$py = $y+ ($table['border_spacing_V']/2);
			$pw = $w- $table['border_spacing_H'];
			$ph = $h- $table['border_spacing_V'];
		  }
		  else {
			$px = $x;
			$py = $y;
			$pw = $w;
			$ph = $h;
		  }
		  if ($this->ColActive) {
			$this->grad->Gradient($px, $py, $pw, $ph, $g['type'], $g['stops'], $g['colorspace'], $g['coords'], $g['extend']);
		  }
		  else {
			$this->tableBackgrounds[$level*9+7][] = array('gradient'=>true, 'x'=>$px, 'y'=>$py, 'w'=>$pw, 'h'=>$ph, 'gradtype'=>$g['type'], 'stops'=>$g['stops'], 'colorspace'=>$g['colorspace'], 'coords'=>$g['coords'], 'extend'=>$g['extend'], 'clippath'=>'');
		  }
		}
	  }
	  else if ($tablehf['background-image']['image_id']) {	// Background pattern
		$n = count($this->patterns)+1;
 		if ($table['borders_separate']) { 
 			$px = $x+ ($table['border_spacing_H']/2);
			$py = $y+ ($table['border_spacing_V']/2);
			$pw = $w- $table['border_spacing_H'];
			$ph = $h- $table['border_spacing_V'];
		}
		else {
			$px = $x;
			$py = $y;
			$pw = $w;
			$ph = $h;
		}
		if ($this->ColActive) {
			list($orig_w, $orig_h, $x_repeat, $y_repeat) = $this->_resizeBackgroundImage($tablehf['background-image']['orig_w'], $tablehf['background-image']['orig_h'], $pw, $ph, $tablehf['background-image']['resize'], $tablehf['background-image']['x_repeat'] , $tablehf['background-image']['y_repeat']);
			$this->patterns[$n] = array('x'=>$px, 'y'=>$py, 'w'=>$pw, 'h'=>$ph, 'pgh'=>$this->h, 'image_id'=>$tablehf['background-image']['image_id'], 'orig_w'=>$orig_w, 'orig_h'=>$orig_h, 'x_pos'=>$tablehf['background-image']['x_pos'] , 'y_pos'=>$tablehf['background-image']['y_pos'] , 'x_repeat'=>$x_repeat, 'y_repeat'=>$y_repeat, 'itype'=>$tablehf['background-image']['itype']);
			if ($tablehf['background-image']['opacity']>0 && $tablehf['background-image']['opacity']<1) { $opac = $this->SetAlpha($tablehf['background-image']['opacity'],'Normal',true); }
			else { $opac = ''; }
			$this->_out(sprintf('q /Pattern cs /P%d scn %s %.3F %.3F %.3F %.3F re f Q', $n, $opac, $px*_MPDFK, ($this->h-$py)*_MPDFK, $pw*_MPDFK, -$ph*_MPDFK));
		}
		else {	// mPDF 5.3.99
			$this->tableBackgrounds[$level*9+8][] = array('x'=>$px, 'y'=>$py, 'w'=>$pw, 'h'=>$ph, 'image_id'=>$tablehf['background-image']['image_id'], 'orig_w'=>$tablehf['background-image']['orig_w'], 'orig_h'=>$tablehf['background-image']['orig_h'], 'x_pos'=>$tablehf['background-image']['x_pos'], 'y_pos'=>$tablehf['background-image']['y_pos'], 'x_repeat'=>$tablehf['background-image']['x_repeat'], 'y_repeat'=>$tablehf['background-image']['y_repeat'], 'clippath'=>'', 'resize'=>$tablehf['background-image']['resize'], 'opacity'=>$tablehf['background-image']['opacity'], 'itype'=>$tablehf['background-image']['itype']);
		}
	  }
	}
/*-- END BACKGROUNDS --*/

   	//Cell Border
 	if ($table['borders_separate'] && $paintcell && $border) { 
 		$this->_tableRect($x+ ($table['border_spacing_H']/2)+($border_details['L']['w'] /2), $y+ ($table['border_spacing_V']/2)+($border_details['T']['w'] /2), $w-$table['border_spacing_H']-($border_details['L']['w'] /2)-($border_details['R']['w'] /2), $h- $table['border_spacing_V']-($border_details['T']['w'] /2)-($border_details['B']['w']/2), $border, $border_details, false, $table['borders_separate']);
	}
 	else if ($paintcell && $border) { 
		$this->_tableRect($x, $y, $w, $h, $border, $border_details, true, $table['borders_separate']);  	// true causes buffer
	}

 	//Print cell content
      //$this->divheight = $this->table_lineheight*$this->lineheight; 
      if (!empty($textbuffer)) {
		if ($horf=='F' && preg_match('/{colsum([0-9]*)[_]*}/', $textbuffer[0][0], $m)) {	// mPDF 5.3.92
			$rep = sprintf("%01.".intval($m[1])."f", $this->colsums[$colctr-1]);
			$textbuffer[0][0] = preg_replace('/{colsum[0-9_]*}/', $rep ,$textbuffer[0][0]);
		}

		if ($R) {
					$cellPtSize = $textbuffer[0][11] / $this->shrin_k;
					if (!$cellPtSize) { $cellPtSize = $this->default_font_size; }
					$cellFontHeight = ($cellPtSize/_MPDFK);
					$opx = $this->x;
					$opy = $this->y;
					$angle = INTVAL($R);
					// Only allow 45 - 90 degrees (when bottom-aligned) or -90
					if ($angle > 90) { $angle = 90; }
					else if ($angle > 0 && (isset($va) && $va!='B')) { $angle = 90; }
					else if ($angle > 0 && $angle <45) { $angle = 45; }
					else if ($angle < 0) { $angle = -90; }
					$offset = ((sin(deg2rad($angle))) * 0.37 * $cellFontHeight);
					if (isset($align) && $align =='R') { 
						$this->x += ($w) + ($offset) - ($cellFontHeight/3) - ($padding['R'] + $border_details['R']['w']); 
					}
					else if (!isset($align ) || $align =='C') { 
						$this->x += ($w/2) + ($offset); 
					}
					else { 
						$this->x += ($offset) + ($cellFontHeight/3)+($padding['L'] + $border_details['L']['w']); 
					}
					// mPDF 5.3.78
					$str = '';
					foreach($tablehf['textbuffer'] AS $t) { $str .= $t[0].' '; }
					$str = trim($str);

					if (!isset($va) || $va=='M') { 
						$this->y -= ($h-$mih)/2; //Undo what was added earlier VERTICAL ALIGN
						if ($angle > 0) { $this->y += (($h-$mih)/2)+($padding['T'] + $border_details['T']['w']) + ($mih-($padding['T'] + $border_details['T']['w']+$border_details['B']['w']+$padding['B'])); }
						else if ($angle < 0) { $this->y += (($h-$mih)/2)+($padding['T'] + $border_details['T']['w']); }
					}
					else if (isset($va) && $va=='B') { 
						$this->y -= $h-$mih; //Undo what was added earlier VERTICAL ALIGN
						if ($angle > 0) { $this->y += $h-($border_details['B']['w']+$padding['B']); }
						else if ($angle < 0) { $this->y += $h-$mih+($padding['T'] + $border_details['T']['w']); }
					}
					else if (isset($va) && $va=='T') { 
						if ($angle > 0) { $this->y += $mih-($border_details['B']['w']+$padding['B']); }
						else if ($angle < 0) { $this->y += ($padding['T'] + $border_details['T']['w']); }
					}

					$this->Rotate($angle,$this->x,$this->y);
					$s_fs = $this->FontSizePt;
					$s_f = $this->FontFamily;
					$s_st = $this->FontStyle;
					// mPDF 5.3.54
					if (!empty($textbuffer[0][3])) { //Font Color
						$cor = $textbuffer[0][3];
						$this->SetTColor($cor);	
					}
					$s_str = $this->strike;
					$this->strike = $textbuffer[0][8]; //Strikethrough
					$this->SetFont($textbuffer[0][4],$textbuffer[0][2],$cellPtSize,true,true);
					$this->Text($this->x,$this->y,$str);
					$this->Rotate(0);
					$this->SetFont($s_f,$s_st,$s_fs,true,true);
					$this->SetTColor(0);	// mPDF 5.3.54	
					$this->strike = $s_str;	// mPDF 5.3.54
					$this->x = $opx;
					$this->y = $opy;
		}
		else {
			if ($table['borders_separate']) {	// NB twice border width
				$xadj = $border_details['L']['w'] + $padding['L'] +($table['border_spacing_H']/2);
				$wadj = $border_details['L']['w'] + $border_details['R']['w'] + $padding['L'] +$padding['R'] + $table['border_spacing_H'];
				$yadj = $border_details['T']['w'] + $padding['T'] + ($table['border_spacing_H']/2);
			}
			else {
				$xadj = $border_details['L']['w']/2 + $padding['L'];
				$wadj = ($border_details['L']['w'] + $border_details['R']['w'])/2 + $padding['L'] + $padding['R'];
				$yadj = $border_details['T']['w']/2 + $padding['T'];
			}

			$this->divwidth=$w-($wadj);
			$this->x += $xadj;
			$this->y += $yadj;
			$this->printbuffer($textbuffer,'',true);
		}

	}
      $textbuffer = array();

/*-- BACKGROUNDS --*/
			if (!$this->ColActive) {
	  		  if (isset($content[$i][0]['trgradients']) && ($colctr==1 || $table['borders_separate'])) { 
				$g = $this->grad->parseBackgroundGradient($content[$i][0]['trgradients']);
				if ($g) {
					$gx = $x0;
					$gy = $y;
					$gh = $h;
					$gw = $table['w'] - ($table['max_cell_border_width']['L']/2) - ($table['max_cell_border_width']['R']/2) - $table['margin']['L'] - $table['margin']['R'];
					if ($table['borders_separate']) { 
						$gw -= ($table['padding']['L'] + $table['border_details']['L']['w'] + $table['padding']['R'] + $table['border_details']['R']['w'] + $table['border_spacing_H']); 
						$s = '';
 						$clx = $x+ ($table['border_spacing_H']/2);
						$cly = $y+ ($table['border_spacing_V']/2);
						$clw = $w- $table['border_spacing_H'];
						$clh = $h- $table['border_spacing_V'];
						// Set clipping path
						$s = ' q 0 w ';	// Line width=0
						$s .= sprintf('%.3F %.3F m ', ($clx)*_MPDFK, ($this->h-($cly))*_MPDFK);	// start point TL before the arc
						$s .= sprintf('%.3F %.3F l ', ($clx)*_MPDFK, ($this->h-($cly+$clh))*_MPDFK);	// line to BL
						$s .= sprintf('%.3F %.3F l ', ($clx+$clw)*_MPDFK, ($this->h-($cly+$clh))*_MPDFK);	// line to BR
						$s .= sprintf('%.3F %.3F l ', ($clx+$clw)*_MPDFK, ($this->h-($cly))*_MPDFK);	// line to TR
						$s .= sprintf('%.3F %.3F l ', ($clx)*_MPDFK, ($this->h-($cly))*_MPDFK);	// line to TL
						$s .= ' W n ';	// Ends path no-op & Sets the clipping path
						$this->tableBackgrounds[$level*9+4][] = array('gradient'=>true, 'x'=>$gx + ($table['border_spacing_H']/2), 'y'=>$gy + ($table['border_spacing_V']/2), 'w'=>$gw - $table['border_spacing_V'], 'h'=>$gh - $table['border_spacing_H'], 'gradtype'=>$g['type'], 'stops'=>$g['stops'], 'colorspace'=>$g['colorspace'], 'coords'=>$g['coords'], 'extend'=>$g['extend'], 'clippath'=>$s);
					}
					else {
						$this->tableBackgrounds[$level*9+4][] = array('gradient'=>true, 'x'=>$gx, 'y'=>$gy, 'w'=>$gw, 'h'=>$gh, 'gradtype'=>$g['type'], 'stops'=>$g['stops'], 'colorspace'=>$g['colorspace'], 'coords'=>$g['coords'], 'extend'=>$g['extend'], 'clippath'=>'');
					}
				}
			    }

			   if (isset($content[$i][0]['trbackground-images']) && ($colctr==1 || $table['borders_separate'])) { 
			    if ($content[$i][0]['trbackground-images']['gradient'] && preg_match('/(-moz-)*(repeating-)*(linear|radial)-gradient/', $content[$i][0]['trbackground-images']['gradient'] )) {
				$g = $this->grad->parseMozGradient( $content[$i][0]['trbackground-images']['gradient'] );
				if ($g) {
					$gx = $x0;
					$gy = $y;
					$gh = $h;
					$gw = $table['w'] - ($table['max_cell_border_width']['L']/2) - ($table['max_cell_border_width']['R']/2) - $table['margin']['L'] - $table['margin']['R'];
					if ($table['borders_separate']) { 
						$gw -= ($table['padding']['L'] + $table['border_details']['L']['w'] + $table['padding']['R'] + $table['border_details']['R']['w'] + $table['border_spacing_H']); 
						$s = '';
 						$clx = $x+ ($table['border_spacing_H']/2);
						$cly = $y+ ($table['border_spacing_V']/2);
						$clw = $w- $table['border_spacing_H'];
						$clh = $h- $table['border_spacing_V'];
						// Set clipping path
						$s = ' q 0 w ';	// Line width=0
						$s .= sprintf('%.3F %.3F m ', ($clx)*_MPDFK, ($this->h-($cly))*_MPDFK);	// start point TL before the arc
						$s .= sprintf('%.3F %.3F l ', ($clx)*_MPDFK, ($this->h-($cly+$clh))*_MPDFK);	// line to BL
						$s .= sprintf('%.3F %.3F l ', ($clx+$clw)*_MPDFK, ($this->h-($cly+$clh))*_MPDFK);	// line to BR
						$s .= sprintf('%.3F %.3F l ', ($clx+$clw)*_MPDFK, ($this->h-($cly))*_MPDFK);	// line to TR
						$s .= sprintf('%.3F %.3F l ', ($clx)*_MPDFK, ($this->h-($cly))*_MPDFK);	// line to TL
						$s .= ' W n ';	// Ends path no-op & Sets the clipping path
						$this->tableBackgrounds[$level*9+4][] = array('gradient'=>true, 'x'=>$gx + ($table['border_spacing_H']/2), 'y'=>$gy + ($table['border_spacing_V']/2), 'w'=>$gw - $table['border_spacing_V'], 'h'=>$gh - $table['border_spacing_H'], 'gradtype'=>$g['type'], 'stops'=>$g['stops'], 'colorspace'=>$g['colorspace'], 'coords'=>$g['coords'], 'extend'=>$g['extend'], 'clippath'=>$s);
					}
					else {
						$this->tableBackgrounds[$level*9+4][] = array('gradient'=>true, 'x'=>$gx, 'y'=>$gy, 'w'=>$gw, 'h'=>$gh, 'gradtype'=>$g['type'], 'stops'=>$g['stops'], 'colorspace'=>$g['colorspace'], 'coords'=>$g['coords'], 'extend'=>$g['extend'], 'clippath'=>'');
					}
				}
			    }
			    else { 
				$image_id = $content[$i][0]['trbackground-images']['image_id'];
				$orig_w = $content[$i][0]['trbackground-images']['orig_w'];
				$orig_h = $content[$i][0]['trbackground-images']['orig_h'];
				$x_pos = $content[$i][0]['trbackground-images']['x_pos'];
				$y_pos = $content[$i][0]['trbackground-images']['y_pos'];
				$x_repeat = $content[$i][0]['trbackground-images']['x_repeat'];
				$y_repeat = $content[$i][0]['trbackground-images']['y_repeat'];
				$resize = $content[$i][0]['trbackground-images']['resize'];
				$opacity = $content[$i][0]['trbackground-images']['opacity'];
				$itype = $content[$i][0]['trbackground-images']['itype'];		// mPDF 5.3.99

				$clippath = '';
				$gx = $x0;
				$gy = $y;
				$gh = $h;
				$gw = $table['w'] - ($table['max_cell_border_width']['L']/2) - ($table['max_cell_border_width']['R']/2) - $table['margin']['L'] - $table['margin']['R'];
				if ($table['borders_separate']) { 
					$gw -= ($table['padding']['L'] + $table['border_details']['L']['w'] + $table['padding']['R'] + $table['border_details']['R']['w'] + $table['border_spacing_H']); 
					$s = '';
 					$clx = $x+ ($table['border_spacing_H']/2);
					$cly = $y+ ($table['border_spacing_V']/2);
					$clw = $w- $table['border_spacing_H'];
					$clh = $h- $table['border_spacing_V'];
					// Set clipping path
					$s = ' q 0 w ';	// Line width=0
					$s .= sprintf('%.3F %.3F m ', ($clx)*_MPDFK, ($this->h-($cly))*_MPDFK);	// start point TL before the arc
					$s .= sprintf('%.3F %.3F l ', ($clx)*_MPDFK, ($this->h-($cly+$clh))*_MPDFK);	// line to BL
					$s .= sprintf('%.3F %.3F l ', ($clx+$clw)*_MPDFK, ($this->h-($cly+$clh))*_MPDFK);	// line to BR
					$s .= sprintf('%.3F %.3F l ', ($clx+$clw)*_MPDFK, ($this->h-($cly))*_MPDFK);	// line to TR
					$s .= sprintf('%.3F %.3F l ', ($clx)*_MPDFK, ($this->h-($cly))*_MPDFK);	// line to TL
					$s .= ' W n ';	// Ends path no-op & Sets the clipping path
					$this->tableBackgrounds[$level*9+5][] = array('x'=>$gx + ($table['border_spacing_H']/2), 'y'=>$gy + ($table['border_spacing_V']/2), 'w'=>$gw - $table['border_spacing_V'], 'h'=>$gh - $table['border_spacing_H'], 'image_id'=>$image_id, 'orig_w'=>$orig_w, 'orig_h'=>$orig_h, 'x_pos'=>$x_pos, 'y_pos'=>$y_pos, 'x_repeat'=>$x_repeat, 'y_repeat'=>$y_repeat, 'clippath'=>$s, 'resize'=>$resize, 'opacity'=>$opacity, 'itype'=>$itype);
				}
				else {
					$this->tableBackgrounds[$level*9+5][] = array('x'=>$gx, 'y'=>$gy, 'w'=>$gw, 'h'=>$gh, 'image_id'=>$image_id, 'orig_w'=>$orig_w, 'orig_h'=>$orig_h, 'x_pos'=>$x_pos, 'y_pos'=>$y_pos, 'x_repeat'=>$x_repeat, 'y_repeat'=>$y_repeat, 'clippath'=>'', 'resize'=>$resize, 'opacity'=>$opacity, 'itype'=>$itype);
				}
			    }
			   }
			}
/*-- END BACKGROUNDS --*/

	// TABLE BORDER - if separate OR collapsed and only table border
	// mPDF 5.3.15 
	if (($table['borders_separate'] || ($this->simpleTables && !$table['simple']['border'])) && $table['border']) { 
			$halfspaceL = $table['padding']['L'] + ($table['border_spacing_H']/2);
			$halfspaceR = $table['padding']['R'] + ($table['border_spacing_H']/2);
			$halfspaceT = $table['padding']['T'] + ($table['border_spacing_V']/2);
			$halfspaceB = $table['padding']['B'] + ($table['border_spacing_V']/2);
			$tbx = $x;
			$tby = $y;
			$tbw = $w;
			$tbh = $h;
			$tab_bord = 0;
			$corner = '';
 			if ($i == $firstrow && $horf=='H') {		// Top
				$tby -= $halfspaceT + ($table['border_details']['T']['w']/2);
				$tbh += $halfspaceT + ($table['border_details']['T']['w']/2);
				$this->setBorder($tab_bord , _BORDER_TOP); 
				$corner .= 'T';
			}
			// mPDF 5.3.15 
			if (($i == ($lastrow) || (isset($tablehf['rowspan']) && ($i+$tablehf['rowspan']) == ($lastrow+1))) && $horf=='F') {	// Bottom
				$tbh += $halfspaceB + ($table['border_details']['B']['w']/2);
				$this->setBorder($tab_bord , _BORDER_BOTTOM); 
				$corner .= 'B';
			}
			if ($colctr == 1 && $firstSpread) {	// Left	// mPDF 5.3.36
				$tbx -= $halfspaceL + ($table['border_details']['L']['w']/2);
				$tbw += $halfspaceL + ($table['border_details']['L']['w']/2);
				$this->setBorder($tab_bord , _BORDER_LEFT); 
				$corner .= 'L';
			}
			if ($colctr == count($content[$i]) && $finalSpread) {	// Right	// mPDF 5.3.36
				$tbw += $halfspaceR + ($table['border_details']['R']['w']/2);
				$this->setBorder($tab_bord , _BORDER_RIGHT); 
				$corner .= 'R';
			}
			$this->_tableRect($tbx, $tby, $tbw, $tbh, $tab_bord , $table['border_details'], false, $table['borders_separate'], 'table', $corner, $table['border_spacing_V'], $table['border_spacing_H'] );
	}


     }// end column $content
     $this->y = $y + $h; //Update y coordinate
   }// end row $i
   unset($table );
   $this->colsums = array();	// mPDF 5.3.92
  }
}
/*-- END TABLES --*/

/*-- HTMLHEADERS-FOOTERS --*/
function SetHTMLHeader($header='',$OE='',$write=false) {

	$height = 0;
	if (is_array($header) && isset($header['html']) && $header['html']) { 
		$Hhtml = $header['html']; 
		if ($this->setAutoTopMargin) {
			if (isset($header['h'])) { $height = $header['h']; }
			else { $height = $this->_gethtmlheight($Hhtml); }
		}
	}
	else if (!is_array($header) && $header) { 
		$Hhtml = $header; 
		if ($this->setAutoTopMargin) { $height = $this->_gethtmlheight($Hhtml); }
	}
	else { $Hhtml = ''; }

	if ($OE != 'E') { $OE = 'O'; }
	if ($OE == 'E') {
	   
	   if ($Hhtml) {
		$this->HTMLHeaderE['html'] = $Hhtml;
		$this->HTMLHeaderE['h'] = $height;
	   }
	   else { $this->HTMLHeaderE = ''; }
	}
	else {
	   
	   if ($Hhtml) {
		$this->HTMLHeader['html'] = $Hhtml;
		$this->HTMLHeader['h'] = $height;
	   }
	   else { $this->HTMLHeader = ''; }
	}
	if (!$this->mirrorMargins && $OE == 'E') { return; }
	if ($Hhtml=='') { return; }
	if ($OE == 'E') {
		$this->headerDetails['even'] = array();	// override and clear any other non-HTML header/footer
	}
	else {
		$this->headerDetails['odd'] = array();	// override and clear any non-HTML other header/footer
	}

	if ($this->setAutoTopMargin=='pad') {
		$this->tMargin = $this->margin_header + $height + $this->orig_tMargin;
		if (isset($this->saveHTMLHeader[$this->page][$OE]['mt'])) { $this->saveHTMLHeader[$this->page][$OE]['mt'] = $this->tMargin; }
	}
	else if ($this->setAutoTopMargin=='stretch') {
		$this->tMargin = max($this->orig_tMargin, $this->margin_header + $height + $this->autoMarginPadding);
		if (isset($this->saveHTMLHeader[$this->page][$OE]['mt'])) { $this->saveHTMLHeader[$this->page][$OE]['mt'] = $this->tMargin; }
	}
	if ($write && $this->state!=0 && (($this->mirrorMargins && $OE == 'E' && ($this->page)%2==0) || ($this->mirrorMargins && $OE != 'E' && ($this->page)%2==1) || !$this->mirrorMargins)) { $this->writeHTMLHeaders(); }
}

function SetHTMLFooter($footer='',$OE='') {

	$height = 0;
	if (is_array($footer) && isset($footer['html']) && $footer['html']) { 
		$Fhtml = $footer['html']; 
		if ($this->setAutoBottomMargin) {
			if (isset($footer['h'])) { $height = $footer['h']; }
			else { $height = $this->_gethtmlheight($Fhtml); }
		}
	}
	else if (!is_array($footer) && $footer) { 
		$Fhtml = $footer; 
		if ($this->setAutoBottomMargin) { $height = $this->_gethtmlheight($Fhtml); }
	}
	else { $Fhtml = ''; }

	if ($OE != 'E') { $OE = 'O'; }
	if ($OE == 'E') {
	   
	   if ($Fhtml) {
		$this->HTMLFooterE['html'] = $Fhtml;
		$this->HTMLFooterE['h'] = $height;
	   }
	   else { $this->HTMLFooterE = ''; }
	}
	else {
	   
	   if ($Fhtml) {
		$this->HTMLFooter['html'] = $Fhtml;
		$this->HTMLFooter['h'] = $height;
	   }
	   else { $this->HTMLFooter = ''; }
	}
	if (!$this->mirrorMargins && $OE == 'E') { return; }
	if ($Fhtml=='') { return false; }
	if ($OE == 'E') {
		$this->footerDetails['even'] = array();	// override and clear any other header/footer
	}
	else {
		$this->footerDetails['odd'] = array();	// override and clear any other header/footer
	}

	if ($this->setAutoBottomMargin=='pad') {
		$this->bMargin = $this->margin_footer + $height + $this->orig_bMargin;
		$this->PageBreakTrigger=$this->h-$this->bMargin ;
		if (isset($this->saveHTMLHeader[$this->page][$OE]['mb'])) { $this->saveHTMLHeader[$this->page][$OE]['mb'] = $this->bMargin; }
	}
	else if ($this->setAutoBottomMargin=='stretch') {
		$this->bMargin = max($this->orig_bMargin, $this->margin_footer + $height + $this->autoMarginPadding);
		$this->PageBreakTrigger=$this->h-$this->bMargin ;
		if (isset($this->saveHTMLHeader[$this->page][$OE]['mb'])) { $this->saveHTMLHeader[$this->page][$OE]['mb'] = $this->bMargin; }
	}
}


function _getHtmlHeight($html) {
		$save_state = $this->state;
		if($this->state==0) {
			$this->AddPage($this->CurOrientation);
		}
		$this->state = 2;
		$this->Reset();
		$this->pageoutput[$this->page] = array();
		$save_x = $this->x;
		$save_y = $this->y;
		$this->x = $this->lMargin;
		$this->y = $this->margin_header;
		$html = str_replace('{PAGENO}',$this->pagenumPrefix.$this->docPageNum($this->page).$this->pagenumSuffix,$html);
		$html = str_replace($this->aliasNbPgGp,$this->nbpgPrefix.$this->docPageNumTotal($this->page).$this->nbpgSuffix,$html );
		$html = str_replace($this->aliasNbPg,$this->page,$html );
		$html = preg_replace('/\{DATE\s+(.*?)\}/e',"date('\\1')",$html );
		$this->HTMLheaderPageLinks = array();
		$this->HTMLheaderPageAnnots = array();
		$this->HTMLheaderPageForms = array();
		$savepb = $this->pageBackgrounds;
		$this->writingHTMLheader = true;
		$this->WriteHTML($html , 4);	// parameter 4 saves output to $this->headerbuffer
		$this->writingHTMLheader = false;
		$h = ($this->y - $this->margin_header);
		$this->Reset();
		$this->pageoutput[$this->page] = array();
		$this->headerbuffer = '';
		$this->pageBackgrounds = $savepb;
		$this->x = $save_x;
		$this->y = $save_y;
		$this->state = $save_state;
		if($save_state==0) {
			unset($this->pages[1]);
			$this->page = 0;
		}
		return $h;
}


// Called internally from Header
function writeHTMLHeaders() {

	if ($this->mirrorMargins && ($this->page)%2==0) { $OE = 'E'; }	// EVEN
	else { $OE = 'O'; }
	if ($OE == 'E') {
		$this->saveHTMLHeader[$this->page][$OE]['html'] = $this->HTMLHeaderE['html'] ;
	}
	else {
		$this->saveHTMLHeader[$this->page][$OE]['html'] = $this->HTMLHeader['html'] ;
	}
	if ($this->forcePortraitHeaders && $this->CurOrientation=='L' && $this->CurOrientation!=$this->DefOrientation) {
		$this->saveHTMLHeader[$this->page][$OE]['rotate'] = true;
		$this->saveHTMLHeader[$this->page][$OE]['ml'] = $this->tMargin;
		$this->saveHTMLHeader[$this->page][$OE]['mr'] = $this->bMargin;
		$this->saveHTMLHeader[$this->page][$OE]['mh'] = $this->margin_header;
		$this->saveHTMLHeader[$this->page][$OE]['mf'] = $this->margin_footer;
		$this->saveHTMLHeader[$this->page][$OE]['pw'] = $this->h;
		$this->saveHTMLHeader[$this->page][$OE]['ph'] = $this->w;
	}
	else {
		$this->saveHTMLHeader[$this->page][$OE]['ml'] = $this->lMargin;
		$this->saveHTMLHeader[$this->page][$OE]['mr'] = $this->rMargin;
		$this->saveHTMLHeader[$this->page][$OE]['mh'] = $this->margin_header;
		$this->saveHTMLHeader[$this->page][$OE]['mf'] = $this->margin_footer;
		$this->saveHTMLHeader[$this->page][$OE]['pw'] = $this->w;
		$this->saveHTMLHeader[$this->page][$OE]['ph'] = $this->h;
	}
}

function writeHTMLFooters() {

	if ($this->mirrorMargins && ($this->page)%2==0) { $OE = 'E'; }	// EVEN
	else { $OE = 'O'; }
	if ($OE == 'E') {
		$this->saveHTMLFooter[$this->page][$OE]['html'] = $this->HTMLFooterE['html'] ;
	}
	else {
		$this->saveHTMLFooter[$this->page][$OE]['html'] = $this->HTMLFooter['html'] ;
	}
	if ($this->forcePortraitHeaders && $this->CurOrientation=='L' && $this->CurOrientation!=$this->DefOrientation) {
		$this->saveHTMLFooter[$this->page][$OE]['rotate'] = true;
		$this->saveHTMLFooter[$this->page][$OE]['ml'] = $this->tMargin;
		$this->saveHTMLFooter[$this->page][$OE]['mr'] = $this->bMargin;
		$this->saveHTMLFooter[$this->page][$OE]['mt'] = $this->rMargin;
		$this->saveHTMLFooter[$this->page][$OE]['mb'] = $this->lMargin;
		$this->saveHTMLFooter[$this->page][$OE]['mh'] = $this->margin_header;
		$this->saveHTMLFooter[$this->page][$OE]['mf'] = $this->margin_footer;
		$this->saveHTMLFooter[$this->page][$OE]['pw'] = $this->h;
		$this->saveHTMLFooter[$this->page][$OE]['ph'] = $this->w;
	}
	else {
		$this->saveHTMLFooter[$this->page][$OE]['ml'] = $this->lMargin;
		$this->saveHTMLFooter[$this->page][$OE]['mr'] = $this->rMargin;
		$this->saveHTMLFooter[$this->page][$OE]['mt'] = $this->tMargin;
		$this->saveHTMLFooter[$this->page][$OE]['mb'] = $this->bMargin;
		$this->saveHTMLFooter[$this->page][$OE]['mh'] = $this->margin_header;
		$this->saveHTMLFooter[$this->page][$OE]['mf'] = $this->margin_footer;
		$this->saveHTMLFooter[$this->page][$OE]['pw'] = $this->w;
		$this->saveHTMLFooter[$this->page][$OE]['ph'] = $this->h;
	}
}
/*-- END HTMLHEADERS-FOOTERS --*/

function DefHeaderByName($name,$arr) {
	if (!$name) { $name = '_default'; }
	$this->pageheaders[$name] = $arr;
}

function DefFooterByName($name,$arr) {
	if (!$name) { $name = '_default'; }
	$this->pagefooters[$name] = $arr;
}

function SetHeaderByName($name,$side='O',$write=false) {
	if (!$name) { $name = '_default'; }
	if ($side=='E') { $this->headerDetails['even'] = $this->pageheaders[$name]; }
	else { $this->headerDetails['odd'] = $this->pageheaders[$name]; }
	if ($write) { $this->Header(); }
}

function SetFooterByName($name,$side='O') {
	if (!$name) { $name = '_default'; }
	if ($side=='E') { $this->footerDetails['even'] = $this->pagefooters[$name]; }
	else { $this->footerDetails['odd'] = $this->pagefooters[$name]; }
}

/*-- HTMLHEADERS-FOOTERS --*/
function DefHTMLHeaderByName($name,$html) {
	if (!$name) { $name = '_default'; }

	$this->pageHTMLheaders[$name]['html'] = $html;
	$this->pageHTMLheaders[$name]['h'] = $this->_gethtmlheight($html);
}

function DefHTMLFooterByName($name,$html) {
	if (!$name) { $name = '_default'; }

	$this->pageHTMLfooters[$name]['html'] = $html;
	$this->pageHTMLfooters[$name]['h'] = $this->_gethtmlheight($html);
}

function SetHTMLHeaderByName($name,$side='O',$write=false) {
	if (!$name) { $name = '_default'; }
	$this->SetHTMLHeader($this->pageHTMLheaders[$name],$side,$write);
}

function SetHTMLFooterByName($name,$side='O') {
	if (!$name) { $name = '_default'; }
	$this->SetHTMLFooter($this->pageHTMLfooters[$name],$side,$write);
}
/*-- END HTMLHEADERS-FOOTERS --*/


function SetHeader($Harray=array(),$side='',$write=false) {
  if (is_string($Harray)) {
    if (strlen($Harray)==0) {
	if ($side=='O') { $this->headerDetails['odd'] = array(); }
	else if ($side=='E') { $this->headerDetails['even'] = array(); }
	else { $this->headerDetails = array(); }
   }
   else if (strpos($Harray,'|') || strpos($Harray,'|')===0) {
	$hdet = explode('|',$Harray);
	$this->headerDetails = array (
  		'odd' => array (
	'L' => array ('content' => $hdet[0], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'C' => array ('content' => $hdet[1], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'R' => array ('content' => $hdet[2], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'line' => $this->defaultheaderline,
  		),
  		'even' => array (
	'R' => array ('content' => $hdet[0], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'C' => array ('content' => $hdet[1], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'L' => array ('content' => $hdet[2], 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'line' => $this->defaultheaderline,
		)
	);
    }
    else {
	$this->headerDetails = array (
  		'odd' => array (
	'R' => array ('content' => $Harray, 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'line' => $this->defaultheaderline,
  		),
  		'even' => array (
	'L' => array ('content' => $Harray, 'font-size' => $this->defaultheaderfontsize, 'font-style' => $this->defaultheaderfontstyle),
	'line' => $this->defaultheaderline,
		)
	);
    }
  }
  else if (is_array($Harray)) {
	if ($side=='O') { $this->headerDetails['odd'] = $Harray; }
	else if ($side=='E') { $this->headerDetails['even'] = $Harray; }
	else { $this->headerDetails = $Harray; }
  }
/*-- HTMLHEADERS-FOOTERS --*/
  // Overwrite any HTML Header previously set
  if ($side=='E') { $this->SetHTMLHeader('','E'); }
  else if ($side=='O') {  $this->SetHTMLHeader(''); }
  else {
	$this->SetHTMLHeader('');
	$this->SetHTMLHeader('','E');
  }
/*-- END HTMLHEADERS-FOOTERS --*/

  if ($write) { 
	$save_y = $this->y;
	$this->Header();
	$this->SetY($save_y) ; 
  }
}




function SetFooter($Farray=array(),$side='') {
  if (is_string($Farray)) {
    if (strlen($Farray)==0) {
	if ($side=='O') { $this->footerDetails['odd'] = array(); }
	else if ($side=='E') { $this->footerDetails['even'] = array(); }
	else { $this->footerDetails = array(); }
    }
    else if (strpos($Farray,'|') || strpos($Farray,'|')===0) {
	$fdet = explode('|',$Farray);
	$this->footerDetails = array (
		'odd' => array (
	'L' => array ('content' => $fdet[0], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'C' => array ('content' => $fdet[1], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'R' => array ('content' => $fdet[2], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'line' => $this->defaultfooterline,
		),
		'even' => array (
	'R' => array ('content' => $fdet[0], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'C' => array ('content' => $fdet[1], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'L' => array ('content' => $fdet[2], 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'line' => $this->defaultfooterline,
		)
	);
    }
    else {
	$this->footerDetails = array (
		'odd' => array (
	'R' => array ('content' => $Farray, 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'line' => $this->defaultfooterline,
		),
		'even' => array (
	'L' => array ('content' => $Farray, 'font-size' => $this->defaultfooterfontsize, 'font-style' => $this->defaultfooterfontstyle),
	'line' => $this->defaultfooterline,
		)
	);
    }
  }
  else if (is_array($Farray)) {
	if ($side=='O') { $this->footerDetails['odd'] = $Farray; }
	else if ($side=='E') { $this->footerDetails['even'] = $Farray; }
	else { $this->footerDetails = $Farray; }
  }
/*-- HTMLHEADERS-FOOTERS --*/
  // Overwrite any HTML Footer previously set
  if ($side=='E') { $this->SetHTMLFooter('','E'); }
  else if ($side=='O') {  $this->SetHTMLFooter(''); }
  else {
	$this->SetHTMLFooter('');
	$this->SetHTMLFooter('','E');
  }
/*-- END HTMLHEADERS-FOOTERS --*/
}

/*-- WATERMARK --*/
function setUnvalidatedText($txt='', $alpha=-1) {
	if ($alpha>=0) $this->watermarkTextAlpha = $alpha;
	$this->watermarkText = $txt;
}
function SetWatermarkText($txt='', $alpha=-1) {
	if ($alpha>=0) $this->watermarkTextAlpha = $alpha;
	$this->watermarkText = $txt;
}

function SetWatermarkImage($src, $alpha=-1, $size='D', $pos='F') {
	if ($alpha>=0) $this->watermarkImageAlpha = $alpha;
	$this->watermarkImage = $src;
	$this->watermark_size = $size;
	$this->watermark_pos = $pos;
}
/*-- END WATERMARK --*/


//Page footer
function Footer() {
/*-- CSS-PAGE --*/
  // PAGED MEDIA - CROP / CROSS MARKS from @PAGE
  if ($this->show_marks == 'CROP' || $this->show_marks == 'CROPCROSS') {
	// Show TICK MARKS
	$this->SetLineWidth(0.1);	// = 0.1 mm
	$this->SetDColor($this->ConvertColor(0));
	$l = $this->cropMarkLength;
	$m = $this->cropMarkMargin;	// Distance of crop mark from margin 
	$b = $this->nonPrintMargin;	// Non-printable border at edge of paper sheet 
	$ax1 = $b;
	$bx = $this->page_box['outer_width_LR'] - $m;
	$ax = max($ax1, $bx-$l);
	$cx1 = $this->w - $b;
	$dx = $this->w - $this->page_box['outer_width_LR'] + $m;
	$cx = min($cx1, $dx+$l);
	$ay1 = $b;
	$by = $this->page_box['outer_width_TB'] - $m;
	$ay = max($ay1, $by-$l);
	$cy1 = $this->h - $b;
	$dy = $this->h - $this->page_box['outer_width_TB'] + $m;
	$cy = min($cy1, $dy+$l);

	$this->Line($ax, $this->page_box['outer_width_TB'], $bx, $this->page_box['outer_width_TB']);
	$this->Line($cx, $this->page_box['outer_width_TB'], $dx, $this->page_box['outer_width_TB']);
	$this->Line($ax, $this->h - $this->page_box['outer_width_TB'], $bx, $this->h - $this->page_box['outer_width_TB']);
	$this->Line($cx, $this->h - $this->page_box['outer_width_TB'], $dx, $this->h - $this->page_box['outer_width_TB']);
	$this->Line($this->page_box['outer_width_LR'], $ay, $this->page_box['outer_width_LR'], $by);
	$this->Line($this->page_box['outer_width_LR'], $cy, $this->page_box['outer_width_LR'], $dy);
	$this->Line($this->w - $this->page_box['outer_width_LR'], $ay, $this->w - $this->page_box['outer_width_LR'], $by);
	$this->Line($this->w - $this->page_box['outer_width_LR'], $cy, $this->w - $this->page_box['outer_width_LR'], $dy);

	if ($this->printers_info) {
		$hd = date('Y-m-d H:i').'  Page '.$this->page.' of {nb}';
		$this->SetTColor($this->ConvertColor(0));
		$this->SetFont('arial','',7.5,true,true);
		$this->x = $this->page_box['outer_width_LR'] + 1.5;
		$this->y = 1;
		$this->Cell($headerpgwidth ,$this->FontSize,$hd,0,0,'L',0,'',0,0,0,'M');
		$this->SetFont($this->default_font,'',$this->original_default_font_size);
	}

  }
  if ($this->show_marks == 'CROSS' || $this->show_marks == 'CROPCROSS') {
	$this->SetLineWidth(0.1);	// = 0.1 mm
	$this->SetDColor($this->ConvertColor(0));
	$l = 14 /2;	// longer length of the cross line (half)
	$w = 6 /2;	// shorter width of the cross line (half)
	$r = 1.2;	// radius of circle
	$m = $this->crossMarkMargin;	// Distance of cross mark from margin 
	$x1 = $this->page_box['outer_width_LR'] - $m;
	$x2 = $this->w - $this->page_box['outer_width_LR'] + $m;
	$y1 = $this->page_box['outer_width_TB'] - $m;
	$y2 = $this->h - $this->page_box['outer_width_TB'] + $m;
	// Left
	$this->Circle($x1, $this->h/2, $r, 'S') ;
	$this->Line($x1-$w, $this->h/2, $x1+$w, $this->h/2);
	$this->Line($x1, $this->h/2-$l, $x1, $this->h/2+$l);
	// Right
	$this->Circle($x2, $this->h/2, $r, 'S') ;
	$this->Line($x2-$w, $this->h/2, $x2+$w, $this->h/2);
	$this->Line($x2, $this->h/2-$l, $x2, $this->h/2+$l);
	// Top
	$this->Circle($this->w/2, $y1, $r, 'S') ;
	$this->Line($this->w/2, $y1-$w, $this->w/2, $y1+$w);
	$this->Line($this->w/2-$l, $y1, $this->w/2+$l, $y1);
	// Bottom
	$this->Circle($this->w/2, $y2, $r, 'S') ;
	$this->Line($this->w/2, $y2-$w, $this->w/2, $y2+$w);
	$this->Line($this->w/2-$l, $y2, $this->w/2+$l, $y2);
  }


	// If @page set non-HTML headers/footers named, they were not read until later in the HTML code - so now set them
	if ($this->page==1) {
		if ($this->firstPageBoxHeader) {
			$this->headerDetails['odd'] = $this->pageheaders[$this->firstPageBoxHeader]; 
  			$this->Header();
		}
		if ($this->firstPageBoxFooter) {
			$this->footerDetails['odd'] = $this->pagefooters[$this->firstPageBoxFooter];
		}
		$this->firstPageBoxHeader='';
		$this->firstPageBoxFooter='';
	}
/*-- END CSS-PAGE --*/



/*-- HTMLHEADERS-FOOTERS --*/
  if (($this->mirrorMargins && ($this->page%2==0) && $this->HTMLFooterE) || ($this->mirrorMargins && ($this->page%2==1) && $this->HTMLFooter) || (!$this->mirrorMargins && $this->HTMLFooter)) {
	$this->writeHTMLFooters(); 
/*-- WATERMARK --*/
  	if (($this->watermarkText) && ($this->showWatermarkText)) {
		$this->watermark( $this->watermarkText, 45, 120, $this->watermarkTextAlpha);	// Watermark text
  	}
	if (($this->watermarkImage) && ($this->showWatermarkImage)) {
		$this->watermarkImg( $this->watermarkImage, $this->watermarkImageAlpha);	// Watermark image
	}
/*-- END WATERMARK --*/
	return;
  }
/*-- END HTMLHEADERS-FOOTERS --*/

  $this->processingHeader=true;
  $this->ResetMargins();	// necessary after columns
  $this->pgwidth = $this->w - $this->lMargin - $this->rMargin;
/*-- WATERMARK --*/
  if (($this->watermarkText) && ($this->showWatermarkText)) {
	$this->watermark( $this->watermarkText, 45, 120, $this->watermarkTextAlpha);	// Watermark text
  }
  if (($this->watermarkImage) && ($this->showWatermarkImage)) {
	$this->watermarkImg( $this->watermarkImage, $this->watermarkImageAlpha);	// Watermark image
  }
/*-- END WATERMARK --*/
  $h = $this->footerDetails;
  if(count($h)) {

	if ($this->forcePortraitHeaders && $this->CurOrientation=='L' && $this->CurOrientation!=$this->DefOrientation) {
		$this->_out(sprintf('q 0 -1 1 0 0 %.3F cm ',($this->h*_MPDFK)));
		$headerpgwidth = $this->h - $this->orig_lMargin - $this->orig_rMargin;
		if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
			$headerlmargin = $this->orig_rMargin;
		}
		else {
			$headerlmargin = $this->orig_lMargin;
		}
	}
	else { 
		$yadj = 0; 
		$headerpgwidth = $this->pgwidth;
		$headerlmargin = $this->lMargin;
	}
	$this->SetY(-$this->margin_footer);

	$this->SetTColor($this->ConvertColor(0));
    	$this->SUP = false;
	$this->SUB = false;
	$this->bullet = false;

	// only show pagenumber if numbering on
	$pgno = $this->docPageNum($this->page, true); 

	if (($this->mirrorMargins) && (($this->page)%2==0)) {	// EVEN
			$side = 'even';
	}
	else {	// ODD	// OR NOT MIRRORING MARGINS/FOOTERS = DEFAULT
			$side = 'odd';
	}
	$maxfontheight = 0;
	foreach(array('L','C','R') AS $pos) {
	  if (isset($h[$side][$pos]['content']) && $h[$side][$pos]['content']) {
		if (isset($h[$side][$pos]['font-size']) && $h[$side][$pos]['font-size']) { $hfsz = $h[$side][$pos]['font-size']; }
		else { $hfsz = $this->default_font_size; }
		$maxfontheight = max($maxfontheight,$hfsz);
	  }
	}
	// LEFT-CENTER-RIGHT
	foreach(array('L','C','R') AS $pos) {
	  if (isset($h[$side][$pos]['content']) && $h[$side][$pos]['content']) {
		$hd = str_replace('{PAGENO}',$pgno,$h[$side][$pos]['content']);
		$hd = str_replace($this->aliasNbPgGp,$this->nbpgPrefix.$this->aliasNbPgGp.$this->nbpgSuffix,$hd);
		$hd = preg_replace('/\{DATE\s+(.*?)\}/e',"date('\\1')",$hd);
		if (isset($h[$side][$pos]['font-family']) && $h[$side][$pos]['font-family']) { $hff = $h[$side][$pos]['font-family']; }
		else { $hff = $this->original_default_font; }
		if (isset($h[$side][$pos]['font-size']) && $h[$side][$pos]['font-size']) { $hfsz = $h[$side][$pos]['font-size']; }
		else { $hfsz = $this->original_default_font_size; }
		$maxfontheight = max($maxfontheight,$hfsz);
		if (isset($h[$side][$pos]['font-style']) && $h[$side][$pos]['font-style']) { $hfst = $h[$side][$pos]['font-style']; }
		else { $hfst = ''; }
		if (isset($h[$side][$pos]['color']) && $h[$side][$pos]['color']) { 
			$hfcol = $h[$side][$pos]['color']; 
			$cor = $this->ConvertColor($hfcol);
			if ($cor) { $this->SetTColor($cor); }
		}
		else { $hfcol = ''; }
		$this->SetFont($hff,$hfst,$hfsz,true,true);
		$this->x = $headerlmargin ;
		$this->y = $this->h - $this->margin_footer - ($maxfontheight/_MPDFK);
		$hd = $this->purify_utf8_text($hd);
		if ($this->text_input_as_HTML) {
			$hd = $this->all_entities_to_utf8($hd);
		}
		// CONVERT CODEPAGE
		if ($this->usingCoreFont) { $hd = mb_convert_encoding($hd,