<?php

declare(strict_types=1);

use App\Helper\StringHelper;
use App\Service\Advertisement\Dto\AdvertisementDto;

/** @var AdvertisementDto $advertisementDto */
$zoneId = $advertisementDto->defaultAdvertisementZoneId; // default zone
if (null !== $advertisementDto->category) {
    $zoneId = $advertisementDto->category->getAdvertisementZoneId();
}

if (empty($zoneId)) {
    return; // skip displaying ad
}

?>

<div class="text-center my-2 d-print-none">
    <script>
        document.MAX_ct0 ='INSERT_CLICKURL_HERE';

        var m3_u = (location.protocol=='https:'?'https://www.jaslo4u.pl/revive-adserver-4.1.4/www/delivery/ajs.php':'http://www.jaslo4u.pl/revive-adserver-4.1.4/www/delivery/ajs.php');
        var m3_r = Math.floor(Math.random()*99999999999);
        if (!document.MAX_used) document.MAX_used = ',';
        document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
        document.write ("?zoneid=<?php echo StringHelper::escape((string) $zoneId); ?>");
        document.write ('&amp;cb=' + m3_r);
        if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
        document.write (document.charset ? '&amp;charset='+document.charset : (document.characterSet ? '&amp;charset='+document.characterSet : ''));
        document.write ("&amp;loc=" + escape(window.location));
        if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
        if (document.context) document.write ("&context=" + escape(document.context));
        if ((typeof(document.MAX_ct0) != 'undefined') && (document.MAX_ct0.substring(0,4) == 'http')) {
            document.write ("&amp;ct0=" + escape(document.MAX_ct0));
        }
        if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
        document.write ("'><\/scr"+"ipt>");
    </script>
    <noscript><a href='http://www.jaslo4u.pl/revive-adserver-4.1.4/www/delivery/ck.php?n=ab5ccf51&amp;cb=INSERT_RANDOM_NUMBER_HERE' target='_blank'><img src='http://www.jaslo4u.pl/revive-adserver-4.1.4/www/delivery/avw.php?zoneid=<?php echo StringHelper::escape((string) $zoneId); ?>&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=ab5ccf51&amp;ct0=INSERT_CLICKURL_HERE' class="border-0" alt='' /></a></noscript>
</div>
