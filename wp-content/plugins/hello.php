<?php
/**
 * @package Hello_Dolly
 * @version 1.7.2
 */
/*
Plugin Name: Hello Dolly
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: Matt Mullenweg
Version: 1.7.2
Author URI: http://ma.tt/
*/

function hello_dolly_get_lyric() {
	/** These are the lyrics to Hello Dolly */
	$lyrics = "“सपनों को पाने की चाहत, मेहनत को अपना साथी बना लेती है।” 
“मुस्कुराहट के साथ दिन की शुरुआत करें, यह आपके दिन को खुशहाल बनाएगा।”
“शिक्षा की जड़ें कड़वी होती हैं लेकिन फल मीठा होता है।”
“उम्मीद की किरण हमेशा अंधेरे को दूर करती है।”
હાથ ની રેખાઓ પર ભરોસો ના કરતા સાહેબ કેમ કે, નસીબ તો એનાય હોય છે જેના હાથ જ નથી હોતા..
આ દુનિયામાં બધું જ કીમતી હોય છે, મેળવ્યા પહેલા તથા ગુમાવ્યા પછી!
અવસર એને જ પ્રાપ્ત થાય છે જેનામાં કાબેલિયત હોય છે.
માત્ર કિનારે ઉભા રહી પાણી જોવાથી નદી પાર નથી થઇ શકતી.
મારા શબ્દકોષમાં ”અસંભવ” નામનો શબ્દ જ નથી
ધીરજ રાખ ભાઈ, અમે ઉડસુ પણ પોતાના ડમ પર.
લક્ષ્ય નહી, રસ્તો બદલી જુઓ, સફળતા જરૂરી મળશે.
સમયને રોકી ન શકાય, પણ સમયનો સદુપયોગ કરી શકાય છે. જો હંમેશા સકારાત્મક વિચારોથી જીવો તો જીવનમાં કંઈક સારું જ થશે.
જીવનનો સૌથી મોટો શિક્ષક સમય છે. એ તમને શાંતિપૂર્વક શીખવે છે કે ખરાબ સમય ક્યારેક જવાની પણ હોય છે.
હું પડવું મંજૂર કરું છું, પણ પાછું ઊભા થવા માટે હંમેશા તૈયાર રહું છું. પરિણામે જીવનમાં હંમેશા સફળતા મળે છે.
માર્ગમાં ક્યારેક કાંટા આવે તો એનો અર્થ એ નથી કે તમે અટકી જશો. કાંટા તો જિંદગીના રસ્તાના ભાગ છે, જે તમને મજબૂત બનાવે છે.
સફળતા તરફ દોરી જતી કોઈ સીધી લાઇન નથી. તે તો સખત મહેનત, ધીરજ અને ધ્યેય પર અડગ રહેવાની કથા છે.
દરેક સવારે એ નવી તક લાવે છે. દરેક પ્રતિકૂળતાને જીતીને સફળતા પ્રાપ્ત કરવી એ જીવનનો સાચો અર્થ છે.
જ્યાં સુધી તમે હાર માનવી નથી, ત્યાં સુધી તમે હાર્યા નથી. જીતવા માટે હંમેશા આગળ વધો અને ક્યારેય રોકાવું નહીં.
સપના જિંદગીની પ્રેરણા છે. જો તમે મહેનત કરશો તો આકાશ પણ તમારા માટે સીમા નહીં રહે.
પ્રતિબદ્ધતા એ છે જે સફળતા તરફના તમારા માર્ગને સમર્થ કરે છે. કઠિન પરિસ્થિતિઓમાં પણ ક્યારેય પાછા ન હટશો.
હું મારા સપના પછી દોડું છું, કારણ કે મેં સખત મહેનત અને નિયમિતતા એ મારા જીવનના બેંનો બનાવ્યાં છે.
જ્યાં સુધી મન મજબૂત છે, ત્યાં સુધી કંઈક પણ મુશ્કેલ નથી. દરેક મુશ્કેલીને જીતવા માટે હંમેશા તૈયાર રહો.
મહેનત કોઈ દિવસ વ્યર્થ નથી જતી. એ સકારાત્મક પરિણામો લાવે છે અને તમારા સપનાને સાકાર કરે છે.
જિંદગીમાં પડકારો તો આવશે જ, પણ એ તમારો માર્ગ નહી અટકાવી શકે. જીવનમાં હંમેશા આગળ વધતા રહો.
મન અને મનોબળ મજબૂત હોય તો કોઈ પણ મુશ્કેલીને પરાજિત કરી શકાય છે. સકારાત્મક વિચારો જ તમને આગળ વધાવે છે.
";

	// Here we split it into lines.
	$lyrics = explode( "\n", $lyrics );

	// And then randomly choose a line.
	return wptexturize( $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ] );
}

// This just echoes the chosen line, we'll position it later.
function hello_dolly() {
	$chosen = hello_dolly_get_lyric();
	$lang   = '';
	if ( 'en_' !== substr( get_user_locale(), 0, 3 ) ) {
		$lang = ' lang="en"';
	}

	printf(
		'<p id="dolly"><span class="screen-reader-text">%s </span><span dir="ltr"%s>%s</span></p>',
		__( 'Quote from Hello Dolly song, by Jerry Herman:' ),
		$lang,
		$chosen
	);
}

// Now we set that function up to execute when the admin_notices action is called.
add_action( 'admin_notices', 'hello_dolly' );

// We need some CSS to position the paragraph.
function dolly_css() {
	echo "
	<style type='text/css'>
	#dolly { float: right; padding: 5px 10px; margin: 0; font-size: 12px; line-height: 1.6666; }
	.rtl #dolly { float: left; }
	.block-editor-page #dolly { display: none; }
	@media screen and (max-width: 782px) {
		#dolly, .rtl #dolly { float: none; padding-left: 0;	padding-right: 0; }
	}
	</style>
	";
}

add_action( 'admin_head', 'dolly_css' );
