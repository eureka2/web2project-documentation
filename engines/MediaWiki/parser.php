<?php /* $Id$ $URL$ */

/* MediaWiki
 * Version 1.0
 * Copyright 2011, Jacques Archim�de (Eureka)
 *
 * This class parses and returns the HTML representation of a document containing 
 * basic MediaWiki-style wiki markup.
 *
 *
 */
require_once "magicwords.class.php";

class MediaWiki
{

  public $wiki = null;
  private $ignore_images = false;
  private $stop = false;
  private $list_level = 0;
  private $preformat = false;
  private $deflist = false;
  private $stop_all = false;
  private $redirect = false;
  private $nowikis = null;
  private $list_level_types = null;
  private $linknumber = 0;
  private $suppress_linebreaks = false;
  private $page_title = '';
  private $page_date;
  private $toc = false;
  private $notoc = false;
  private $forcetoc = false;
  private $mintoclevel = 6;
  private $sections = array();
  private $categories = array();
  private $templates = array();
  private $emphasis = array();
  private $references = array();
  private $parameters = array();
  private $newwindow = false;
  public $languageNames = array(
  'aa' => 'Qafár af',  # Afar
  'ab' => 'Аҧсуа',  # Abkhaz, should possibly add ' бысжѡа'
  'ace' => 'Acèh',  # Aceh
  'af' => 'Afrikaans',  # Afrikaans
  'ak' => 'Akan',    # Akan
  'aln' => 'Gegë',  # Gheg Albanian
  'als' => 'Alemannisch',  # Alemannic -- not a valid code, for compatibility. See gsw.
  'am' => 'አማርኛ',  # Amharic
  'an' => 'Aragonés',  # Aragonese
  'ang' => 'Ænglisc',  # Old English (Bug 23283)
  'ar' => 'العربية',  # Arabic
  'arc' => 'ܐܪܡܝܐ',  # Aramaic
  'arn' => 'Mapudungun',  # Mapuche, Mapudungu, Araucanian (Araucano)
  'arz' => 'مصرى',  # Egyptian Spoken Arabic
  'as' => 'অসমীয়া',  # Assamese
  'ast' => 'Asturianu',  # Asturian
  'av' => 'Авар',  # Avar
  'avk' => 'Kotava', # Kotava
  'ay' => 'Aymar aru',  # Aymara
  'az' => 'Azərbaycanca',  # Azerbaijani
  'ba' => 'Башҡорт',  # Bashkir
  'bar' => 'Boarisch',  # Bavarian (Austro-Bavarian and South Tyrolean)
  'bat-smg' => 'Žemaitėška', # Samogitian
  'bcc' => 'بلوچی مکرانی', # Southern Balochi
  'bcl' => 'Bikol Central', # Bikol: Central Bicolano language
  'be' => 'Беларуская',  #  Belarusian normative
  'be-tarask' => 'Беларуская (тарашкевіца)',  # Belarusian in Taraskievica orthography
  'be-x-old' => 'Беларуская (тарашкевіца)',  # Belarusian in Taraskievica orthography; compat link
  'bg' => 'Български',  # Bulgarian
  'bh' => 'भोजपुरी',  # Bhojpuri
  'bi' => 'Bislama',    # Bislama
  'bm' => 'Bamanankan',  # Bambara
  'bn' => 'বাংলা',  # Bengali
  'bo' => 'བོད་ཡིག',  # Tibetan
  'bpy' => 'ইমার ঠার/বিষ্ণুপ্রিয়া মণিপুরী',  # Bishnupriya Manipuri
  'bqi' => 'بختياري',  # Bakthiari
  'br' => 'Brezhoneg',  # Breton
  'bs' => 'Bosanski',    # Bosnian
  'bug' => 'ᨅᨔ ᨕᨘᨁᨗ',  # Bugis
  'bxr' => 'Буряад',  # Buryat (Russia)
  'ca' => 'Català',  # Catalan
  'cbk-zam' => 'Chavacano de Zamboanga',  # Zamboanga Chavacano
  'cdo' => 'Mìng-dĕ̤ng-ngṳ̄',  # Min Dong
  'ce' => 'Нохчийн',  # Chechen
  'ceb' => 'Cebuano',     # Cebuano
  'ch' => 'Chamoru',    # Chamorro
  'cho' => 'Choctaw',    # Choctaw
  'chr' => 'ᏣᎳᎩ', # Cherokee
  'chy' => 'Tsetsêhestâhese',  # Cheyenne
  'ckb' => 'Soranî / کوردی',  # Sorani
  'ckb-latn' => "\xE2\x80\xAASoranî (latînî)\xE2\x80\xAC", # Central Kurdish Latin script
  'ckb-arab' => "\xE2\x80\xABکوردی (عەرەبی)\xE2\x80\xAC", # Central Kurdish Arabic script
  'co' => 'Corsu',    # Corsican
  'cps' => 'Capiceño', # Capiznon
  'cr' => 'Nēhiyawēwin / ᓀᐦᐃᔭᐍᐏᐣ',    # Cree
  'crh' => 'Qırımtatarca',   # Crimean Tatar
  'crh-latn' => "\xE2\x80\xAAQırımtatarca (Latin)\xE2\x80\xAC",       # Crimean Tatar (Latin)
  'crh-cyrl' => "\xE2\x80\xAAКъырымтатарджа (Кирилл)\xE2\x80\xAC",       # Crimean Tatar (Cyrillic)
  'cs' => 'Česky',  # Czech
  'csb' => 'Kaszëbsczi',  # Cassubian
  'cu' => 'Словѣ́ньскъ / ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ',  # Old Church Slavonic (ancient language)
  'cv' => 'Чӑвашла',  # Chuvash
  'cy' => 'Cymraeg',    # Welsh
  'da' => 'Dansk',    # Danish
  'de' => 'Deutsch',    # German ("Du")
  'de-at' => 'Österreichisches Deutsch',    # Austrian German
  'de-ch' => 'Schweizer Hochdeutsch',    # Swiss Standard German
  'de-formal' => 'Deutsch (Sie-Form)',    # German - formal address ("Sie")
  'diq' => 'Zazaki',    # Zazaki
  'dk' => 'Dansk (deprecated:da)',    # Unused code currently falls back to Danish, 'da' is correct for the language
  'dsb' => 'Dolnoserbski', # Lower Sorbian
  'dv' => 'ދިވެހިބަސް',    # Dhivehi
  'dz' => 'ཇོང་ཁ',    # Bhutani
  'ee' => 'Eʋegbe',  # Éwé
  'el' => 'Ελληνικά',  # Greek
  'eml' => 'Emiliàn e rumagnòl',  # Emiliano-Romagnolo / Sammarinese
  'en' => 'English',    # English
  'en-gb' => 'British English',    # British English
  'eo' => 'Esperanto',  # Esperanto
  'es' => 'Español',  # Spanish
  'et' => 'Eesti',    # Estonian
  'eu' => 'Euskara',    # Basque
  'ext' => 'Estremeñu', # Extremaduran
  'fa' => 'فارسی',  # Persian
  'ff' => 'Fulfulde',    # Fulfulde, Maasina
  'fi' => 'Suomi',    # Finnish
  'fiu-vro' => 'Võro',    # Võro (deprecated code, 'vro' in ISO 639-3 since 2009-01-16)
  'fj' => 'Na Vosa Vakaviti',  # Fijian
  'fo' => 'Føroyskt',  # Faroese
  'fr' => 'Français',  # French
  'frc' => 'Français cadien', # Cajun French
  'frp' => 'Arpetan',  # Franco-Provençal/Arpitan
  'frr' => 'Frasch',  # North Frisian
  'fur' => 'Furlan',    # Friulian
  'fy' => 'Frysk',    # Frisian
  'ga' => 'Gaeilge',    # Irish
  'gag' => 'Gagauz',    # Gagauz
  'gan' => '贛語',    # Gan
  'gan-hans' => '赣语(简体)',  # Gan (Simplified Han)
  'gan-hant' => '贛語(繁體)',  # Gan (Traditional Han)
  'gd' => 'Gàidhlig',  # Scots Gaelic
  'gl' => 'Galego',    # Galician
  'glk' => 'گیلکی',  # Gilaki
  'gn' => 'Avañe\'ẽ',  # Guaraní, Paraguayan
  'got' => '𐌲𐌿𐍄𐌹𐍃𐌺',  # Gothic
  'grc' => 'Ἀρχαία ἑλληνικὴ', # Ancient Greek
  'gsw' => 'Alemannisch',  # Alemannic
  'gu' => 'ગુજરાતી',  # Gujarati
  'gv' => 'Gaelg',    # Manx
  'ha' => 'هَوُسَ',  # Hausa
  'hak' => 'Hak-kâ-fa',  # Hakka
  'haw' => 'Hawai`i',    # Hawaiian
  'he' => 'עברית',  # Hebrew
  'hi' => 'हिन्दी',  # Hindi
  'hif' => 'Fiji Hindi',  # Fijian Hindi (falls back to hif-latn)
  'hif-deva' => 'फ़ीजी हिन्दी',  # Fiji Hindi (devangari)
  'hif-latn' => 'Fiji Hindi',  # Fiji Hindi (latin)
  'hil' => 'Ilonggo',  # Hiligaynon
  'ho' => 'Hiri Motu',  # Hiri Motu
  'hr' => 'Hrvatski',    # Croatian
  'hsb' => 'Hornjoserbsce',  # Upper Sorbian
  'ht'  => 'Kreyòl ayisyen',    # Haitian Creole French
  'hu' => 'Magyar',    # Hungarian
  'hy' => 'Հայերեն',  # Armenian
  'hz' => 'Otsiherero',  # Herero
  'ia' => 'Interlingua',  # Interlingua (IALA)
  'id' => 'Bahasa Indonesia',  # Indonesian
  'ie' => 'Interlingue',  # Interlingue (Occidental)
  'ig' => 'Igbo',      # Igbo
  'ii' => 'ꆇꉙ',  # Sichuan Yi
  'ik' => 'Iñupiak',  # Inupiak (Inupiatun, Northwest Alaska / Inupiatun, North Alaskan)
  'ike-cans' => 'ᐃᓄᒃᑎᑐᑦ',  # Inuktitut, Eastern Canadian/Eastern Canadian "Eskimo"/"Eastern Arctic Eskimo"/Inuit (Unified Canadian Aboriginal Syllabics)
  'ike-latn' => 'inuktitut',  # Inuktitut, Eastern Canadian (Latin script)
  'ilo' => 'Ilokano',  # Ilokano
  'inh' => 'ГІалгІай Ğalğaj',    # Ingush
  'io' => 'Ido',      # Ido
  'is' => 'Íslenska',  # Icelandic
  'it' => 'Italiano',    # Italian
  'iu' => 'ᐃᓄᒃᑎᑐᑦ/inuktitut',  # Inuktitut (macro language - do no localise, see ike/ikt - falls back to ike-cans)
  'ja' => '日本語',  # Japanese
  'jbo' => 'Lojban',    # Lojban
  'jut' => 'Jysk',  # Jutish / Jutlandic
  'jv' => 'Basa Jawa',  # Javanese
  'ka' => 'ქართული',  # Georgian
  'kaa' => 'Qaraqalpaqsha',  # Karakalpak
  'kab' => 'Taqbaylit',  # Kabyle
  'kg' => 'Kongo',    # Kongo, (FIXME!) should probaly be KiKongo or KiKoongo
  'ki' => 'Gĩkũyũ',  # Gikuyu
  'kiu' => 'Kırmancki',  # Kirmanjki
  'kj' => 'Kwanyama',    # Kwanyama
  'kk' => 'Қазақша',  # Kazakh
  'kk-arab' => "\xE2\x80\xABقازاقشا (تٴوتە)\xE2\x80\xAC",  # Kazakh Arabic
  'kk-cyrl' => "\xE2\x80\xAAҚазақша (кирил)\xE2\x80\xAC",  # Kazakh Cyrillic
  'kk-latn' => "\xE2\x80\xAAQazaqşa (latın)\xE2\x80\xAC",  # Kazakh Latin
  'kk-cn' => "\xE2\x80\xABقازاقشا (جۇنگو)\xE2\x80\xAC",  # Kazakh (China)
  'kk-kz' => "\xE2\x80\xAAҚазақша (Қазақстан)\xE2\x80\xAC",  # Kazakh (Kazakhstan)
  'kk-tr' => "\xE2\x80\xAAQazaqşa (Türkïya)\xE2\x80\xAC",  # Kazakh (Turkey)
  'kl' => 'Kalaallisut',  # Inuktitut, Greenlandic/Greenlandic/Kalaallisut (kal)
  'km' => 'ភាសាខ្មែរ',  # Khmer, Central
  'kn' => 'ಕನ್ನಡ',  # Kannada
  'ko' => '한국어',  # Korean
  'ko-kp' => '한국어 (조선)',  # Korean (DPRK)
  'koi' => 'Перем Коми', # Komi-Permyak
  'kr' => 'Kanuri',    # Kanuri, Central
  'krc' => 'Къарачай-Малкъар', # Karachay-Balkar
  'kri' => 'Krio', # Krio
  'krj' => 'Kinaray-a', # Kinaray-a
  'ks' => 'कश्मीरी - (كشميري)',  # Kashmiri
  'ksh' => 'Ripoarisch',  # Ripuarian 
  'ku'  => 'Kurdî',  # Kurdish
  'ku-latn' => "\xE2\x80\xAAKurdî (latînî)\xE2\x80\xAC",  # Northern Kurdish Latin script
  'ku-arab' => "\xE2\x80\xABكوردي (عەرەبی)\xE2\x80\xAC",  # Northern Kurdish Arabic script
  'kv' => 'Коми',  # Komi-Zyrian, cyrillic is common script but also written in latin script
  'kw' => 'Kernewek',    # Cornish
  'ky' => 'Кыргызча',  # Kirghiz
  'la' => 'Latina',    # Latin
  'lad' => 'Ladino',  # Ladino
  'lb' => 'Lëtzebuergesch',  # Luxemburguish
  'lbe' => 'Лакку',  # Lak
  'lez' => 'Лезги',  # Lezgi
  'lfn' => 'Lingua Franca Nova',  # Lingua Franca Nova
  'lg' => 'Luganda',    # Ganda
  'li' => 'Limburgs',  # Limburgian
  'lij' => 'Líguru',  # Ligurian
  'lmo' => 'Lumbaart',  # Lombard
  'ln' => 'Lingála',    # Lingala
  'lo' => 'ລາວ',# Laotian
  'loz' => 'Silozi', # Lozi
  'lt' => 'Lietuvių',  # Lithuanian
  'ltg' => 'Latgaļu',   # Latgalian
  'lv' => 'Latviešu',  # Latvian
  'lzh' => '文言',  # Literary Chinese -- (bug 8217) lzh instead of zh-classical, http://www.sil.org/iso639-3/codes.asp?order=639_3&letter=l
  'lzz' => 'Lazuri',  # Laz
  'mai' => 'मैथिली', # Maithili
  'map-bms' => 'Basa Banyumasan', # Banyumasan 
  'mdf' => 'Мокшень',    # Moksha
  'mg' => 'Malagasy',    # Malagasy
  'mh' => 'Ebon',      # Marshallese
  'mhr' => 'Олык Марий',  # Eastern Mari
  'mi' => 'Māori',  # Maori
  'mk' => 'Македонски',  # Macedonian
  'ml' => 'മലയാളം',  # Malayalam
  'mn' => 'Монгол',  # Halh Mongolian (Cyrillic) (ISO 639-3: khk)
  'mo' => 'Молдовеняскэ',  # Moldovan
  'mr' => 'मराठी',  # Marathi
  'mrj' => 'Кырык мары',  # Hill Mari
  'ms' => 'Bahasa Melayu',  # Malay
  'mt' => 'Malti',  # Maltese
  'mus' => 'Mvskoke',  # Muskogee/Creek
  'mwl' => 'Mirandés',  # Mirandese
  'my' => 'မြန်မာဘာသာ',    # Burmese
  'myv' => 'Эрзянь',  # Erzya
  'mzn' => 'مازِرونی',    # Mazanderani
  'na' => 'Dorerin Naoero',    # Nauruan
  'nah' => 'Nāhuatl',    # Nahuatl, en:Wikipedia writes Nahuatlahtolli, while another form is Náhuatl
  'nan' => 'Bân-lâm-gú', # Min-nan -- (bug 8217) nan instead of zh-min-nan, http://www.sil.org/iso639-3/codes.asp?order=639_3&letter=n
  'nap' => 'Nnapulitano',  # Neapolitan
  'nb' => "\xE2\x80\xAANorsk (bokmål)\xE2\x80\xAC",    # Norwegian (Bokmal)
  'nds' => 'Plattdüütsch',  # Low German ''or'' Low Saxon
  'nds-nl' => 'Nedersaksisch',  # Dutch Low Saxon
  'ne' => 'नेपाली',  # Nepali
  'new' => 'नेपाल भाषा',    # Newar / Nepal Bhasa
  'ng' => 'Oshiwambo',    # Ndonga
  'niu' => 'Niuē',  # Niuean
  'nl' => 'Nederlands',  # Dutch
  'nn' => "\xE2\x80\xAANorsk (nynorsk)\xE2\x80\xAC",  # Norwegian (Nynorsk)
  'no' => "\xE2\x80\xAANorsk (bokmål)\xE2\x80\xAC",    # Norwegian
  'nov' => 'Novial',    # Novial
  'nrm' => 'Nouormand',  # Norman
  'nso' => 'Sesotho sa Leboa',  # Northern Sotho
  'nv' => 'Diné bizaad',  # Navajo
  'ny' => 'Chi-Chewa',  # Chichewa
  'oc' => 'Occitan',    # Occitan
  'om' => 'Oromoo',    # Oromo
  'or' => 'ଓଡ଼ିଆ',    # Oriya
  'os' => 'Иронау', # Ossetic
  'pa' => 'ਪੰਜਾਬੀ', # Eastern Punjabi (pan)
  'pag' => 'Pangasinan',  # Pangasinan
  'pam' => 'Kapampangan',   # Pampanga
  'pap' => 'Papiamentu',  # Papiamentu
  'pcd' => 'Picard',  # Picard
  'pdc' => 'Deitsch',  # Pennsylvania German
  'pdt' => 'Plautdietsch',  # Plautdietsch/Mennonite Low German
  'pfl' => 'Pfälzisch',  # Palatinate German
  'pi' => 'पािऴ',  # Pali
  'pih' => 'Norfuk / Pitkern', # Norfuk/Pitcairn/Norfolk
  'pl' => 'Polski',    # Polish
  'pms' => 'Piemontèis',  # Piedmontese
  'pnb' => 'پنجابی',  # Western Punjabi
  'pnt' => 'Ποντιακά',  # Pontic/Pontic Greek
  'prg' => 'Prūsiskan',  # Prussian
  'ps' => 'پښتو',  # Pashto, Northern/Paktu/Pakhtu/Pakhtoo/Afghan/Pakhto/Pashtu/Pushto/Yusufzai Pashto
  'pt' => 'Português',  # Portuguese
  'pt-br' => 'Português do Brasil',  # Brazilian Portuguese
  'qu' => 'Runa Simi',  # Quechua
  'rgn' => 'Rumagnôl',  # Romagnol
  'rif' => 'Tarifit',  # Tarifit
  'rm' => 'Rumantsch',  # Raeto-Romance
  'rmy' => 'Romani',  # Vlax Romany
  'rn' => 'Kirundi',    # Rundi/Kirundi/Urundi
  'ro' => 'Română',  # Romanian
  'roa-rup' => 'Armãneashce', # Aromanian
  'roa-tara' => 'Tarandíne',  # Tarantino
  'ru' => 'Русский',  # Russian
  'rue' => 'русиньскый язык',  # Rusyn
  'ruq' => 'Vlăheşte',  # Megleno-Romanian (falls back to ruq-latn)
  'ruq-cyrl' => 'Влахесте',  # Megleno-Romanian (Cyrillic script)
  #'ruq-grek' => 'Βλαεστε',  # Megleno-Romanian (Greek script)
  'ruq-latn' => 'Vlăheşte',  # Megleno-Romanian (Latin script)
  'rw' => 'Kinyarwanda',  # Kinyarwanda, should possibly be Kinyarwandi
  'sa' => 'संस्कृत',  # Sanskrit
  'sah' => 'Саха тыла', # Sakha
  'sc' => 'Sardu',    # Sardinian
  'scn' => 'Sicilianu',  # Sicilian
  'sco' => 'Scots',       # Scots
  'sd' => 'سنڌي',  # Sindhi
  'sdc' => 'Sassaresu',  # Sassarese
  'se' => 'Sámegiella',  # Northern Sami
  'sei' => 'Cmique Itom',  # Seri
  'sg' => 'Sängö',    # Sango/Sangho
  'sh' => 'Srpskohrvatski / Српскохрватски', # Serbocroatian
  'shi' => 'Tašlḥiyt',    # Tachelhit
  'si' => 'සිංහල',  # Sinhalese
  'simple' => 'Simple English',  # Simple English
  'sk' => 'Slovenčina',  # Slovak
  'sl' => 'Slovenščina',  # Slovenian
  'sli' => 'Schläsch',  # Lower Selisian
  'sm' => 'Gagana Samoa',  # Samoan
  'sma' => 'Åarjelsaemien',  # Southern Sami
  'sn' => 'chiShona',    # Shona
  'so' => 'Soomaaliga',  # Somali
  'sq' => 'Shqip',    # Albanian
  'sr' => 'Српски / Srpski',  # Serbian
  'sr-ec' => 'Српски (ћирилица)',  # Serbian Cyrillic ekavian
  'sr-el' => 'Srpski (latinica)',  # Serbian Latin ekavian
  'srn' => 'Sranantongo',    # Sranan Tongo
  'ss' => 'SiSwati',    # Swati
  'st' => 'Sesotho',    # Southern Sotho
  'stq' => 'Seeltersk',    # Saterland Frisian
  'su' => 'Basa Sunda',  # Sundanese
  'sv' => 'Svenska',    # Swedish
  'sw' => 'Kiswahili',  # Swahili
  'szl' => 'Ślůnski',  # Silesian
  'ta' => 'தமிழ்',  # Tamil
  'tcy' => 'ತುಳು', # Tulu
  'te' => 'తెలుగు',  # Telugu
  'tet' => 'Tetun',  # Tetun
  'tg' => 'Тоҷикӣ',  # Tajiki (falls back to tg-cyrl)
  'tg-cyrl' => 'Тоҷикӣ',  # Tajiki (Cyrllic script) (default)
  'tg-latn' => 'tojikī',  # Tajiki (Latin script)
  'th' => 'ไทย',  # Thai
  'ti' => 'ትግርኛ',    # Tigrinya
  'tk' => 'Türkmençe',  # Turkmen
  'tl' => 'Tagalog',    # Tagalog
  'tn' => 'Setswana',    # Setswana
  'to' => 'lea faka-Tonga',    # Tonga (Tonga Islands)
  'tokipona' => 'Toki Pona',      # Toki Pona
  'tp' => 'Toki Pona (deprecated:tokipona)',  # Toki Pona - non-standard language code
  'tpi' => 'Tok Pisin',  # Tok Pisin
  'tr' => 'Türkçe',  # Turkish
  'ts' => 'Xitsonga',    # Tsonga
  'tt' => 'Татарча/Tatarça',  # Tatar (multiple scripts - defaults to Cyrillic)
  'tt-cyrl' => 'Татарча',  # Tatar (Cyrillic script)
  'tt-latn' => 'Tatarça',  # Tatar (Latin script)
  'tum' => 'chiTumbuka',  # Tumbuka
  'tw' => 'Twi',      # Twi, (FIXME!)
  'ty' => 'Reo Mā`ohi',  # Tahitian
  'tyv' => 'Тыва дыл',  # Tyvan
  'udm' => 'Удмурт',  # Udmurt
  'ug' => 'Uyghurche‎ / ئۇيغۇرچە',  # Uyghur (multiple scripts - defaults to Latin)
  'ug-arab' => 'ئۇيغۇرچە', # Uyghur (Arabic script)
  'ug-latn' => 'Uyghurche‎', # Uyghur (Latin script - default)
  'uk' => 'Українська',  # Ukrainian
  'ur' => 'اردو',  # Urdu
  'uz' => 'O\'zbek',  # Uzbek
  've' => 'Tshivenda',    # Venda
  'vec' => 'Vèneto',  # Venetian
  'vep' => 'Vepsan kel\'',  # Veps
  'vi' => 'Tiếng Việt',  # Vietnamese
  'vls' => 'West-Vlams', # West Flemish
  'vmf' => 'Mainfränkisch', # Upper Franconian, Main-Franconian
  'vo' => 'Volapük',  # Volapük
  'vot' => 'Vaďďa',  # Vod/Votian
  'vro' => 'Võro',    # Võro
  'wa' => 'Walon',    # Walloon
  'war' => 'Winaray', # Waray-Waray
  'wo' => 'Wolof',    # Wolof
  'wuu' => '吴语',    # Wu Chinese
  'xal' => 'Хальмг',    # Kalmyk-Oirat (Kalmuk, Kalmuck, Kalmack, Qalmaq, Kalmytskii Jazyk, Khal:mag, Oirat, Volga Oirat, European Oirat, Western Mongolian)
  'xh' => 'isiXhosa',    # Xhosan
  'xmf' => 'მარგალური',  # Mingrelian
  'yi' => 'ייִדיש',  # Yiddish
  'yo' => 'Yorùbá',  # Yoruba
  'yue' => '粵語',  # Cantonese -- (bug 8217) yue instead of zh-yue, http://www.sil.org/iso639-3/codes.asp?order=639_3&letter=y
  'za' => 'Vahcuengh',  # Zhuang
  'zea' => 'Zeêuws',  # Zeeuws/Zeaws
  'zh' => '中文',            # (Zhōng Wén) - Chinese
  'zh-classical' => '文言',      # Classical Chinese/Literary Chinese -- (see bug 8217)
  'zh-cn' => "\xE2\x80\xAA中文(中国大陆)\xE2\x80\xAC",  # Chinese (PRC)
  'zh-hans' => "\xE2\x80\xAA中文(简体)\xE2\x80\xAC",  # Chinese written using the Simplified Chinese script
  'zh-hant' => "\xE2\x80\xAA中文(繁體)\xE2\x80\xAC",  # Chinese written using the Traditional Chinese script
  'zh-hk' => "\xE2\x80\xAA中文(香港)\xE2\x80\xAC",  # Chinese (Hong Kong)
  'zh-min-nan' => 'Bân-lâm-gú',        # Min-nan -- (see bug 8217)
  'zh-mo' => "\xE2\x80\xAA中文(澳門)\xE2\x80\xAC",  # Chinese (Macau)
  'zh-my' => "\xE2\x80\xAA中文(马来西亚)\xE2\x80\xAC",  # Chinese (Malaysia)
  'zh-sg' => "\xE2\x80\xAA中文(新加坡)\xE2\x80\xAC",  # Chinese (Singapore)
  'zh-tw' => "\xE2\x80\xAA中文(台灣)\xE2\x80\xAC",  # Chinese (Taiwan)
  'zh-yue' => '粵語',          # Cantonese -- (see bug 8217)
  'zu' => 'isiZulu'    # Zulu
);

  public function __construct($wiki) {
    $this->wiki = $wiki;
    $this->ignore_images = false;
  }

  private function handle_sections(&$matches) {
    $level = strlen($matches[1]);
    if ($level < $this->mintoclevel) $this->mintoclevel = $level;
    $content = $matches[2];
    $this->sections[] = array($level, $content);
    $this->stop = true;
    $output = "";
    if (sizeof($this->sections) == 1) $output .= "__DEFAULTTOC__";
    $output .= '<span class="mw-headline" id="'.$this->wiki_link($content).'">'."<h{$level}>{$content}</h{$level}>".'</span>';
    // avoid accidental run-on emphasis
    return $this->emphasize_off() . "\n\n".$output."\n\n";
  }

  private function handle_toc() {
    $this->toc = true;
    return "__TOC__";
  }

  private function handle_notoc() {
    $this->notoc = true;
    return "";
  }

  private function handle_forcetoc() {
    $this->forcetoc = true;
    return "";
  }

  private function handle_newline(&$matches) {
    if ($this->suppress_linebreaks) return $this->emphasize_off();
    
    $this->stop = true;
    // avoid accidental run-on emphasis
    return $this->emphasize_off() . "<br /><br />";
  }
  
  private function handle_list($matches, $close=false) {
    $listtypes = array(
      '*'=>'ul',
      '#'=>'ol',
    );
    $output = "";
    $newlevel = ($close) ? 0 : strlen($matches[1]);
    while ($this->list_level!=$newlevel) {
      $listchar = substr($matches[1],-1);
      $listtype = $listtypes[$listchar];
      if ($this->list_level>$newlevel) {
        $listtype = '/'.array_pop($this->list_level_types);
        $this->list_level--;
      } else {
        $this->list_level++;
        array_push($this->list_level_types,$listtype);
      }
      $output .= "\n<{$listtype}>\n";
    }
    if ($close) return $output;
    $output .= "<li>".$matches[2]."</li>\n";
    return $output;
  }
  
  private function handle_definitionlist($matches, $close=false) {
    if ($close) {
      $this->deflist = false;
      return "</dl>\n";
    }
    $output = "";
    if (!$this->deflist) $output .= "<dl>\n";
    $this->deflist = true;
    switch($matches[1]) {
      case ';':
        $term = $matches[2];
        $p = strpos($term,' :');
        if ($p!==false) {
          list($term,$definition) = explode(':',$term);
          $output .= "<dt>{$term}</dt><dd>{$definition}</dd>";
        } else {
          $output .= "<dt>{$term}</dt>";
        }
        break;
      case ':':
        $definition = $matches[2];
        $output .= "<dd>{$definition}</dd>\n";
        break;
    }
    return $output;
  }

  private function handle_preformat($matches, $close=false) {
    if ($close) {
      $this->preformat = false;
      return "</pre>\n";
    }
    $this->stop_all = true;
    $output = "";
    if (!$this->preformat) $output .= "<pre>";
    $this->preformat = true;
    $output .= $matches[1];
    return $output."\n";
  }
  
  private function handle_horizontalrule(&$matches) {
    return "<hr />";
  }

  private function wiki_link($topic) {
    return ucfirst(str_replace(' ', '_', trim($topic)));
  }

  private function handle_image($namespace, $href, $options) {
    if ($this->ignore_images) return "";
    $alt = "";
    $title = "";
    $imageattr = array();
    $containerstyle = array();
    $containertype = "normal";
    foreach ($options as $k=>$option) {
      if (preg_match("/^alt=(.*)/", $option, $T)) {
        $alt = $T[1];
      } elseif (preg_match("/^(.*)px$/", $option, $T)) {
        $imageattr[] = 'width="'.$T[1].'"';
      } elseif ($option == "left" || $option == "right") {
        $containerstyle[] = 'float: '.$option;
      } elseif ($option == "center") {
        $containerstyle[] = 'text-align: '.$option;
      } elseif (in_array($option, array('thumb', 'thumbnail'))) {
        $containertype = $option;
        $imagepath = realpath('modules/documentation/images/upload/'.$href);
        list($imgWidth, $imgHeight, $imgType, $imgAttr) = getimagesize($imagepath);
        $thumbHeight = $imgHeight;
        $thumbWidth = $imgWidth;
        if($thumbWidth > 220) {
          $thumbHeight = round($thumbHeight * 220 / $thumbWidth, 0);
          $thumbWidth = 220;
        }
        $imageattr[] = 'width="'.$thumbWidth.'"';
        $imageattr[] = 'height="'.$thumbHeight.'"';
        $imageattr[] = 'class="thumbimage"';
      } elseif ($option == 'frame') {
        $containertype = $option;
      } elseif ($option != 'none') {
        $title = $option;
      }
    }
    $imagetag = $this->wiki->imageTag($href, $alt, implode(" ", $imageattr));
    switch($containertype) {
      case 'frame':
        $imagetag = sprintf(
          '<div style="float: right; background-color: #F5F5F5; border: 1px solid #D0D0D0; padding: 2px %s">'.
            '%s'.
            '<div>%s</div>'.
          '</div>',
          implode(" ", $containerstyle),
          $imagetag,
          $title
        );
        break;
      case 'thumb':
      case 'thumbnail':
        $imagetag = sprintf(
          '<div class="thumb tright" style="%s">'.
            '<div class="thumbinner" style="width:222px;">'.
              '<a href="modules/documentation/%s:%s" class="image">'.
                '%s'.
              '</a>'.
              '<div class="thumbcaption">'.
                '<div class="magnify">'.
                  '<a href="modules/documentation/%s:%s" class="internal" title="Agrandir">'.
                    '<img src="modules/documentation/images/magnify-clip.png" width="15" height="11" alt="" />'.
                  '</a>'.
                '</div>'.
                '%s'.
              '</div>'.
            '</div>'.
          '</div>',
          implode(" ", $containerstyle),
          $namespace,
          $href,
          $imagetag,
          $namespace,
          $href,
          $title
        );
        break;
      default:
        $imagetag = sprintf(
          '<div style="%s">'.
            '%s'.
            '<div>%s</div>'.
          '</div>',
          implode(" ", $containerstyle),
          $imagetag,
          $title
        );
    }
    return $imagetag;
  }

  private function handle_internallink($input) {
    global $AppUI;
    $tokens = explode("|", trim($input));
    $href = array_shift($tokens);
    if (!$href) {
      if (empty($tokens)) {
        return "";
      }
      $href = $tokens[0];
    }
    list($namespace, $href) = explode(":", $href);
    if (!$href) {
      $href = $namespace;
      $namespace = "";
    }
    if ($namespace=='File'||$namespace==$AppUI->_('File')||
        $namespace=='Media'||$namespace==$AppUI->_('Media')||
        $namespace=='Image'||$namespace==$AppUI->_('Image')) {
      return $this->handle_image($namespace, $href, $tokens);
    }
    $title = empty($tokens) ? "" : $tokens[0] ? array_shift($tokens) : $href;
    $title = preg_replace('/\(.*?\)/s','',$title);
    $title = preg_replace('/^.*?\:/s','',$title);
    $href = ($namespace?$namespace:$this->wiki->getNamespace()).':'.$this->wiki_link($href);
    if ($namespace=='Category' || $namespace==$AppUI->_('Category')) {
      $this->categories[] = array($href, $title);
      return "";
    }
    return $this->wiki->internalLinkTag($href, $title);
  }

  private function handle_implicitexternallink(&$matches) {
    $href = $matches[1];
    $scheme = $matches[2];
    $this->newwindow = true;
    return sprintf(
      '<a class="external free link-%s" href="%s" rel="nofollow"%s>%s</a>',
      $scheme,
      $href,
      ($this->newwindow?' target="_blank"':''),
      $href
    );
  }

  private function handle_externallink(&$matches) {
    $href = $matches[2];
    $title = $matches[3];
    if (!$title) {
      $this->linknumber++;
      $title = "[{$this->linknumber}]";
    }
    $this->newwindow = true;
    $output = sprintf(
      '<a class="external" href="%s"%s>%s</a>',
      $href,
      ($this->newwindow?' target="_blank"':''),
      $title
    );
    return $output;
  }

  private function emphasize($amount) {
    $amounts = array(
      2=>array('<em>','</em>'),
      3=>array('<strong>','</strong>'),
      4=>array('<strong>','</strong>'),
      5=>array('<em><strong>','</strong></em>'),
    );
    $output = "";
    // handle cases where emphasized phrases end in an apostrophe, eg: ''somethin'''
    // should read <em>somethin'</em> rather than <em>somethin<strong>
    if ( (!$this->emphasis[$amount]) && ($this->emphasis[$amount-1]) ) {
      $amount--;
      $output = "'";
    }
    $output .= $amounts[$amount][(int) $this->emphasis[$amount]];
    $this->emphasis[$amount] = !$this->emphasis[$amount];
    return $output;
  }

  private function handle_emphasize(&$matches) {
    $amount = strlen($matches[1]);
    return $this->emphasize($amount);
  }

  private function emphasize_off() {
    $output = "";
    foreach ($this->emphasis as $amount=>$state) {
      if ($state) $output .= $this->emphasize($amount);
    }
    return $output;
  }

  private function handle_eliminate(&$matches) {
    return "";
  }

  private function handle_reference($name, $input) {
    $this->references[] = array($name, $input);
    $numref = sizeof($this->references);
    $numnote = $numref - 1;
    return '<sup id="cite_ref-'.$numnote.'" class="reference"><a href="#cite_note-'.$numnote.'"><span class="cite_crochet">[</span>'.$numref.'<span class="cite_crochet">]</span></a></sup>';
  }

  private function parse_references($input) {
    $regex = '#<ref(\s*name=([^>]+))?>((?:[^<]|<(?!/?ref(\s*name=([^>]+))?>)|(?R))+)</ref>#';
    if (is_array($input)) {
      $input = $this->handle_reference($input[2], $input[3]);
    }
    return preg_replace_callback($regex, array(&$this, 'parse_references'), $input);
  }

  private function handle_comment(&$matches) {
    return "";
  }

  private function parse_comments($input) {
    $regex = '#\<\!--((?:[^\<\>]|<(?!\!--)|(?<!--)\>|(?R))+)--\>#';
    if (is_array($input)) {
      $input = $this->handle_comment($input);
    }
    return preg_replace_callback($regex, array(&$this, 'parse_comments'), $input);
  }

  private function handle_noinclude(&$matches) {
    return "";
  }

  private function parse_noinclude($input) {
    $regex = '#\<noinclude>((?:[^[]|\<(?!/?noinclude>)|(?R))+)\</noinclude>#';
    if (is_array($input)) {
      $input = $this->handle_noinclude($input);
    }
    return preg_replace_callback($regex, array(&$this, 'parse_noinclude'), $input);
  }

  private function handle_includeonly(&$matches) {
    return "";
  }

  private function parse_includeonly($input) {
    $regex = '#\<includeonly>((?:[^[]|\<(?!/?includeonly>)|(?R))+)\</includeonly>#';
    if (is_array($input)) {
      $input = $this->handle_includeonly($input);
    }
    return preg_replace_callback($regex, array(&$this, 'parse_includeonly'), $input);
  }

  private function replace_parameters($input) {
    $tokens = preg_split("/(\{|\})/", $input, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $before = $parameter = "";
    while(!empty($tokens)) {
      $token = array_shift($tokens);
      if ($token == "{" && count($tokens)>1 && $tokens[0] == "{" && $tokens[1] == "{") {
        array_shift($tokens);
        array_shift($tokens);
        break;
      }
      $before .= $token;
    }
    $ndelim = 1;
    while(!empty($tokens)) {
      $token = array_shift($tokens);
      if ($token == "{" && count($tokens)>1 && $tokens[0] == "{" && $tokens[1] == "{") {
        $parameter .= $token.$token;
        $token = array_shift($tokens);
        $token = array_shift($tokens);
        $ndelim++;
      } elseif ($token == "}" && count($tokens)>1 && $tokens[0] == "}" && $tokens[1] == "}") {
        $ndelim--;
        $token = array_shift($tokens);
        $token = array_shift($tokens);
        if ($ndelim == 0) {
          break;
        }
        $parameter .= $token.$token;
      }
      $parameter .= $token;
    }
    if (!$parameter) {
      return $input;
    }
    list($name, $default) = explode("|", $parameter);
    $value = isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    return $this->replace_parameters($before.$value.implode("", $tokens));
  }

  private function handle_inbraces($inbraces) {
    global $AppUI;
    $input = $this->parse_doublebraces($inbraces, "handle_inbraces");
    $magicwords = new MagicWords($this);
    if ($input == $AppUI->_('References')) {
      return '<'.$AppUI->_('References').'/>';
    } elseif (preg_match("/^\s*([A-Z]+)\s*$/s", $input, $T)) {
      if (is_callable(array($magicwords, "handle_".$T[1]))) {
        $func = "handle_".$T[1];
        return $magicwords->$func();
      }
    } elseif (preg_match("/^\s*#([^:]+):(.*)$/s", $input, $T)) {
      $func = "handle_".$T[1];
      return is_callable(array($magicwords, $func)) ? $magicwords->$func($T[2]) : "";
    } elseif (preg_match("/^#\s*\w+:/s", $input)) {
      return "";
    } elseif (preg_match("/^\s*([^:]+):(.*)$/s", $input, $T)) {
      if (is_callable(array($magicwords, "handle_".$T[1]))) {
        $func = "handle_".$T[1];
        return $magicwords->$func($T[2]);
      }
    }
    $parameters = explode("|", preg_replace("/[\r\n]/", "", $input));
    $template = array_shift($parameters);
    if (isset($this->languageNames[$template])) {
      return '<span style="font-family: monospace; font-weight: bold; font-size: small;">(<abbr class="abbr" title="'.$AppUI->_('Language').'&nbsp;: '.$this->languageNames[$template].'">'.$template.'</abbr>)</span>';
    }
    $nowiki = false;
    if (preg_match("/^msgnw:(.*)/", $template, $T)) {
      $template = $T[1];
      $nowiki = true;
    }
    $template = $this->wiki_link($template);
    $wikipage = $this->wiki->loadTemplate($template);
    if ($nowiki) {
      return $this->handle_save_nowiki(array("", $wikipage->content()));
    } else {
      $parser = new MediaWiki($this->wiki);
      return $wikipage->content() ?
        $parser->parse($wikipage->content(), $wikipage->wikipage_title, strtotime($wikipage->wikipage_date), $parameters, true) :
        "";
    }
  }

  private function parse_doublebraces($input, $handler) {
    $tokens = preg_split("/(\{|\})/", $input, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $before = $indelims = "";
    while(!empty($tokens)) {
      $token = array_shift($tokens);
      if ($token == "{" && !empty($tokens) && $tokens[0] == "{") {
        array_shift($tokens);
        break;
      }
      $before .= $token;
    }
    $ndelim = 1;
    while(!empty($tokens)) {
      $token = array_shift($tokens);
      if ($token == "{" && !empty($tokens) && $tokens[0] == "{") {
        $indelims .= $token;
        $token = array_shift($tokens);
        $ndelim++;
      } elseif ($token == "}" && !empty($tokens) && $tokens[0] == "}") {
        $ndelim--;
        $token = array_shift($tokens);
        if ($ndelim == 0) {
          break;
        }
        $indelims .= "}";
      }
      $indelims .= $token;
    }
    if (!$indelims) {
      return $input;
    }
    return $this->parse_doublebraces($before.$this->$handler($indelims).implode("", $tokens), $handler);
  }

  private function parse_doublesquarebrackets($input, $handler) {
    $tokens = preg_split("/(\[|\])/", $input, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $before = $indelims = "";
    while(!empty($tokens)) {
      $token = array_shift($tokens);
      if ($token == "[" && !empty($tokens) && $tokens[0] == "[") {
        array_shift($tokens);
        break;
      }
      $before .= $token;
    }
    $ndelim = 1;
    while(!empty($tokens)) {
      $token = array_shift($tokens);
      if ($token == "[" && !empty($tokens) && $tokens[0] == "[") {
        $indelims .= $token;
        $token = array_shift($tokens);
        $ndelim++;
      } elseif ($token == "]" && !empty($tokens) && $tokens[0] == "]") {
        $ndelim--;
        $token = array_shift($tokens);
        if ($ndelim == 0) {
          break;
        }
        $indelims .= "]";
      }
      $indelims .= $token;
    }
    if (!$indelims) {
      return $input;
    }
    return $this->parse_doublesquarebrackets($before.$this->$handler($indelims).implode("", $tokens), $handler);
  }

  private function handle_cell($cell) {
    $tag = "td";
    if ($cell{0} == '!') {
      $tag = "th";
      $cell = substr($cell, 1);
    } elseif ($cell{0} == '|') {
      $cell = substr($cell, 1);
    }
    list($attr, $content) = explode("|", $cell);
    if (!$content) {
      $content = $attr;
      $attr = "";
    } else {
      $attr = " ". trim($attr);
    }
    $output = "<{$tag}{$attr}>".trim($content);
    $output .= "</{$tag}>"; 
    return $output;
  }

  private function parse_cells($input) {
    $cells = preg_split("#\n#", $input);
    $output = "";
    foreach($cells as $cell) {
      $output .= $this->handle_cell($cell);
    }
    return $output;
  }

  private function handle_row($row) {
    $output = "<tr>";
    $output .= $this->parse_cells($row);
    $output .= "</tr>"; 
    return $output;
  }

  private function parse_rows($input) {
    $rows = explode("\n|-\n", $input);
    $output = "";
    foreach($rows as $row) {
      $output .= $this->handle_row($row);
    }
    return $output;
  }

  private function handle_table(&$matches) {
    $rows = $this->parse_tables($matches[1]);
    if (($p = strpos($rows, "\n|-") === false)) {
      return "";
    }
    list ($head, $rows) = explode("\n|-", $rows, 2);
    list ($attr, $caption) = explode("\n|+", $head, 2);
    $rows = substr($rows, 1);
    $output = "<table";
    if ($attr) $output .= " ".trim($attr);
    $output .= ">"; 
    if ($caption) $output .= "<caption>".$caption."</caption>";
    $output .= "<tbody>"; 
    $output .= $this->parse_rows($rows);
    $output .= "</tbody>"; 
    $output .= "</table>"; 
    return $output;
  }

  private function parse_tables($input) {
    $regex = '#\{\|((?:[^\{\}\|]|\|(?!\})|(?R))+)\n\|\}#';
    if (is_array($input)) {
      $input = $this->handle_table($input);
    }
    return preg_replace_callback($regex, array(&$this, 'parse_tables'), $input);
  }

  private function tocnumber(&$number, $level) {
    $output = array();
    $level -= $this->mintoclevel;
    $number[$level]++;
    for ($i = 0; $i < 6; $i++) {
      if ($i <= $level) {
        $output[] = $number[$i];
      } elseif ($i > $level) {
        $number[$i] = 0;
      }
    }
    return implode(".", $output);
  }

  private function make_toc() {
    global $AppUI;
    $output = '<table id="toc" class="toc">'."\n";
    $output .= "  <tbody>\n";
    $output .= "    <tr>\n";
    $output .= "      <td>\n";
    $output .= '        <div id="toctitle">'."\n";
    $output .= "          <h2>".$AppUI->_('Summary')."</h2>\n";
    $output .= '          <span class="toctoggle">[<a href="javascript:void()" class="internal" id="togglelink">'.$AppUI->_('hide')."</a>]</span>\n";
    $output .= "        </div>\n";
    $prevlevel = 0;
    $number = array(0, 0, 0, 0, 0, 0);
    $s = 1;
    foreach($this->sections as $k => $section) {
      $level = $section[0];
      $content = $section[1];
      if ($prevlevel < $level) {
        $output .= '        <ul>'."\n";
      } elseif ($prevlevel > $level) {
        $output .= '        </ul>'."\n";
        $output .= '          </li>'."\n";
      }
      $output .= '          <li class="toclevel-'.$level.' tocsection-'.$s++.'"><a href="#'.$this->wiki_link($content).'"><span class="tocnumber">'.$this->tocnumber($number, $level).'</span> <span class="toctext">'.$content.'</span></a></li>'."\n";
      $prevlevel = $level;
    }
    $output .= "        </ul>\n";
    $output .= "      </td>\n";
    $output .= "    </tr>\n";
    $output .= "  </tbody>\n";
    $output .= "</table>\n";
    $output .= '<script type="text/javascript">'."\n";
    $output .= "//<![CDATA[\n";
    $output .= 'var tocShowText = "'.$AppUI->_('show').'";'."\n";
    $output .= 'var tocHideText = "'.$AppUI->_('hide').'";'."\n";
    $output .= "jQuery('#togglelink').click(function() {\n";
    $output .= "  jQuery('#toc ul').eq(0).toggle();\n";
    $output .= "  jQuery('#togglelink').text(jQuery('#toc ul').eq(0).is(':hidden') ? tocShowText : tocHideText);\n";
    $output .= "});\n"; 
    $output .= "//]]>\n";
    $output .= "</script>\n";
    return $output;
  }

  private function make_references() {
    $output = "";
    $r = 0;
    while($r < sizeof($this->references)) {
      $reference = $this->references[$r];
      $name = $reference[0];
      $input = $reference[1];
      $output .= '<li id="cite_note-'.$r.'"><span class="noprint renvois_vers_le_texte"><a href="#cite_ref-'.$r.'">↑ </a></span>'.$this->parse_references($input).'</li>'."\n";
      $r++;
    }
    return '<ol>'.$output.'</ol>';
  }

  private function make_categories() {
    global $AppUI;
    if (sizeof($this->categories) == 0) return "";
    $output = array();
    foreach($this->categories as $r => $category) {
      $href = $category[0];
      $title = $category[1];
      $output[] = '<span dir="ltr">'.$this->wiki->internalLinkTag($href, $title).'</span>';
    }
    return '<div id="catlinks" class="catlinks"><div id="mw-normal-catlinks">'.
            $this->wiki->internalLinkTag(
                'Special:Categories', 
                sizeof($output) > 1 ? $AppUI->_('Categories') : $AppUI->_('Category'), 
                $AppUI->_('Special').':'.$AppUI->_('Categories')
            ).
            '&nbsp;: '.implode(' | ', $output).'</div></div>';
  }

  private function make_footer() {
    global $AppUI;
    $output  = '<div class="footer">'."\n";
    $output .= '  <ul class="footer-info">'."\n";
    $output .= '    <li class="footer-info-lastmod">'.sprintf($AppUI->_('This page was last modified on %s.'), strftime($AppUI->_('Time format'), $this->page_date)).'<br /></li>'."\n";
    $output .= "  </ul>\n";
    $output .= '  <div style="clear:both"></div>'."\n";
    $output .= "</div>\n";
    return $output;
  }

  private function parse_line($line) {
    $line_regexes = array(
      'preformat'=>'^\s(.*?)$',
      'definitionlist'=>'^([\;\:])\s*(.*?)$',
      'newline'=>'^$',
      'list'=>'^([\*\#]+)(.*?)$',
      'sections'=>'^(={1,6})(.*?)(={1,6})$',
      'horizontalrule'=>'^----$',
    );
    $char_regexes = array(
      'implicitexternallink'=>'(?<![\["])((https?|gopher|mailto|news|ftp|irc):\/\/[^\s\[\<\{\(]+)',
      'externallink'=>'(\[([^\]]*?)(\s+[^\]]*?)?\])',
      'emphasize'=>'(\'{2,5})',
      'eliminate'=>'(__NOEDITSECTION__)',
      'toc'=>'(__TOC__)',
      'notoc'=>'(__NOTOC__)',
      'forcetoc'=>'(__FORCETOC__)'
    );
    $this->stop = false;
    $this->stop_all = false;
    $called = array();
    $line = rtrim($line);
    foreach ($line_regexes as $func=>$regex) {
      if (preg_match("/$regex/i",$line,$matches)) {
        $called[$func] = true;
        $func = "handle_".$func;
        $line = $this->$func($matches);
        if ($this->stop || $this->stop_all) break;
      }
    }
    if (!$this->stop_all) {
      $this->stop = false;
      foreach ($char_regexes as $func=>$regex) {
        $line = preg_replace_callback("/$regex/i",array(&$this,"handle_".$func),$line);
        if ($this->stop) break;
      }
    }
    $isline = strlen(trim($line))>0;
    // if this wasn't a list item, and we are in a list, close the list tag(s)
    if (($this->list_level>0) && !$called['list']) $line = $this->handle_list(false,true) . $line;
    if ($this->deflist && !$called['definitionlist']) $line = $this->handle_definitionlist(false,true) . $line;
    if ($this->preformat && !$called['preformat']) $line = $this->handle_preformat(false,true) . $line;
    // suppress linebreaks for the next line if we just displayed one; otherwise re-enable them
    if ($isline) $this->suppress_linebreaks = (isset($called['newline']) || isset($called['sections']));
    return $line;
  }

  public function parse($text, $title = "", $date = null, $parameters = array(), $istemplate = false) {
    global $AppUI;
    $text2 = $text;
    $this->redirect = false;
    $this->nowikis = array();
    $this->list_level_types = array();
    $this->list_level = 0;
    $this->deflist = false;
    $this->linknumber = 0;
    $this->suppress_linebreaks = false;
    $this->page_title = $title;
    $this->page_date = $date;
    $this->parameters = array();
    foreach($parameters as $p => $parameter) {
      list($name, $value) = explode("=", $parameter);
      if ($value) {
        $this->parameters[$name] = $value;
      } else {
        $this->parameters[$p] = $name;
      }
    }
    $output = "";
    $text = str_replace(
        array("<pre>", "<pre ", "</pre>"),
        array("<nowiki><pre>", "<nowiki><pre ", "</pre></nowiki>"),
        $text
    );
    if ($this->wiki->getNamespace()!='Template') {
      $text = $this->parse_noinclude($text);
      $text = preg_replace("#</?includeonly>#", "", $text);
    } else {
      $text = $this->parse_includeonly($text);
      $text = preg_replace("#</?noinclude>#", "", $text);
    }
    $text = preg_replace_callback(
        '#<nowiki>([\s\S]+)</nowiki>#U',
        array(&$this,"handle_save_nowiki"),
        $text
    );
    $text = $this->replace_parameters($text);
    $text = $this->parse_comments($text);
    $text = preg_replace('#\r\n?#', "\n", $text);
    $text = preg_replace("#!!#", "\n!", $text);
    $text = preg_replace("#\|\|#", "\n|", $text);
    $text = preg_replace("#&nbsp;#", " ", $text);
    $text = $this->parse_tables($text);
    $text = $this->parse_doublesquarebrackets($text, "handle_internallink");
    $text = preg_replace("#\{\{\n#", "{{", $text);
    $text = preg_replace("#\n\}\}#", "}}", $text);
    $text = $this->parse_doublebraces($text, "handle_inbraces");
    $lines = explode("\n",$text);
    if (preg_match('/^\#REDIRECT\s+\[\[(.*?)\]\]$/s',trim($lines[0]),$matches)) {
      $this->redirect = $matches[1];
    }
    foreach ($lines as $k => $line) {
      $line = $this->parse_line($line);
      $output .= $line;
    }
    $output = $this->parse_references($output);
    $output = preg_replace_callback(
        '#<'.$AppUI->_('References').'/>#',
        array(&$this,"make_references"),
        $output
    );
    if ($this->notoc) {
      $output = str_replace("__DEFAULTTOC__", "", $output);
      $output = str_replace("__TOC__", "", $output);
    } elseif ($this->forcetoc) {
      $output = preg_replace_callback('/__DEFAULTTOC__/', array(&$this,"make_toc"), $output);
      $output = str_replace("__TOC__", "", $output);
    } elseif ($this->toc) {
      $output = preg_replace_callback('/__TOC__/', array(&$this,"make_toc"), $output);
      $output = str_replace("__DEFAULTTOC__", "", $output);
    } else {
      $output = preg_replace_callback('/__DEFAULTTOC__/', array(&$this,"make_toc"), $output);
    }
    $output = preg_replace_callback('#<nowiki></nowiki>#', array(&$this,"handle_restore_nowiki"), $output);
    if (!$istemplate) {
      $output .= $this->make_categories();
      $output .= $this->make_footer();
    }
    return $output;
  }

  private function handle_inbraces2($inbraces) {
    global $AppUI;
    $input = $this->parse_doublebraces($inbraces, "handle_inbraces2");
    $magicwords = new MagicWords($this);
    if ($input == $AppUI->_('References')) {
      return "";
    } elseif (preg_match("/^\s*([A-Z]+)\s*$/s", $input, $T)) {
      if (is_callable(array($magicwords, "handle_".$T[1]))) {
        $func = "handle_".$T[1];
        return "";
      }
    } elseif (preg_match("/^\s*#([^:]+):(.*)$/s", $input, $T)) {
      return "";
    } elseif (preg_match("/^#\s*\w+:/s", $input)) {
      return "";
    } elseif (preg_match("/^\s*([^:]+):(.*)$/s", $input, $T)) {
      if (is_callable(array($magicwords, "handle_".$T[1]))) {
        return "";
      }
    }
    $parameters = explode("|", preg_replace("/[\r\n]/", "", $input));
    $template = array_shift($parameters);
    if (isset($this->languageNames[$template])) {
      return "";
    }
    if (preg_match("/^msgnw:(.*)/", $template, $T)) {
      $template = $T[1];
    }
    $template = $this->wiki_link($template);
    $wikipage = $this->wiki->loadTemplate($template);
    if ($wikipage->check() === null) {
      $this->templates[] = array($wikipage->wikipage_name, $wikipage->wikipage_title);
    }
  }

  public function getTemplates($text) {
    $this->templates = array();
    $text = $this->parse_noinclude($text);
    $text = preg_replace("#</?includeonly>#", "", $text);
    $text = preg_replace_callback(
        '#<nowiki>([\s\S]+)</nowiki>#U',
        array(&$this,"handle_save_nowiki"),
        $text
    );
    $text = $this->parse_comments($text);
    $text = preg_replace('#\r\n?#', "\n", $text);
    $text = preg_replace("#!!#", "\n!", $text);
    $text = preg_replace("#\|\|#", "\n|", $text);
    $text = preg_replace("#&nbsp;#", " ", $text);
    $text = preg_replace("#\{\{\n#", "{{", $text);
    $text = preg_replace("#\n\}\}#", "}}", $text);
    $text = $this->parse_doublebraces($text, "handle_inbraces2");
    return $this->templates;
  }

  public function getCategories() {
    return $this->categories;
  }

  private function handle_save_nowiki($matches) {
    array_push($this->nowikis,$matches[1]);
    return "<nowiki></nowiki>";
  }

  private function handle_restore_nowiki($matches) {
    return array_shift($this->nowikis);
  }

}
