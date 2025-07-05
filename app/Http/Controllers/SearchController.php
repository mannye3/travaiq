<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\GenerativeAi;

class SearchController extends Controller
{
    public function showSearchForm()
    {
        return view('location-search');
    }

    public function search1(Request $request)
    {
        $term = $request->input('term');
        
        if (empty($term)) {
            return response()->json([]);
        }

        // Step 1: Get city suggestions with browser-like headers
        $suggestionResponse = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/91.0.4472.124 Safari/537.36',
            'Cookie' => 'agoda.user.03=UserId=cfd674be-8057-4d6f-822b-c7bf6e0f5c81; agoda.prius=PriusID=0&PointsMaxTraffic=Agoda; agoda.price.01=PriceView=1; _fbp=fb.1.1747255400191.110289736142583136; FPID=FPID2.2.3fNFiFLhlwxGUX3tl8ReET22KM9%2B%2FPZu9l%2BsCoVmunM%3D.1747255402; agoda_ptnr_tracking=73c2d7f7-c987-42f7-8fc5-59d832f9517b; ai_user=AZS7E3QxNyALZTyevoYQd6|2025-05-14T20:44:22.442Z; agoda.version.03=CookieId=407a6398-48f3-4068-8eb3-d65cd0b41b8c&DLang=en-us&CurLabel=USD; agoda.familyMode=Mode=0; __RequestVerificationToken=ql40Y0A_J3s9AK5t_dLhoz383AuKI58SQAqnqcpbLIac0XF6Uwsksb0qu0PxN4WNJQ6ROuQnf19sfn4_dq3d0ZxOaoU1; _gid=GA1.2.890101482.1749716159; ul.session=7e7d4ccf-4579-4504-b2b0-9cc7f0e06e9c; ASP.NET_SessionId=gat1js5rtrwpqdsw2tgd43m3; xsrf_token=CfDJ8Dkuqwv-0VhLoFfD8dw7lYzvlw_S31Yy07x5NBPLTUrWCJL9CS4GMyYOrOWCKbZr3m6J9thqkSnXe-fDI49jnSpPUpfbmzrXzASMTMeSzBGAxoj3Vb0gzx7ycnMOr4zLURZoHdHvYp0BHavsZbmB6jk; ul.pc.token=eyJhbGciOiJFUzI1NiJ9.eyJtIjo0MjYzMDk5MjMsInIiOlsiMTI3XzM0ODM4NSJdLCJlIjoiNWRpQCpBSG9AaGQ7L0ZFbkgrMCZSPGAlMTpMKTcyXCJmVWYxMzBOcVpiblxcSS5Ea1pvS2tSTk8wUl1QS0A0QXNVX0s8VzhaalhSXFxybk9FaEoiLCJzcmMiOiJzcmMiLCJzdWIiOiJRaHJZakduZFJpR2lOVnFlbWFOVEl3IiwianRpIjoic1VqNEJ1SV9SNjZZLXVLZFNPSEYyQSIsImlhdCI6MTc0OTcxNjE3OSwiZXhwIjoxNzU3NDkyMTc5LCJ3bHQiOiJmMWE1OTA1Zi05NjIwLTQ1ZTUtOWQ5MS1kMjUxYzA3ZTBiNDIiLCJzIjoyfQ.cB3OvEXFhsFLjsPZ6kbfEQSCG8y65_c8XA2izK5t6q20B-blWP4qcpmn4efYT2YPG2Q7PPUxfm-Rk3E7yoUlYQ; agoda.firstclicks=1942345||||2025-06-12T15:17:37||kb12fm2xbjnnlqatjpf0f5ng||{"IsPaid":true,"gclid":"","Type":""}; ASP.NET_SessionId=kb12fm2xbjnnlqatjpf0f5ng; tealiumEnable=true; FPLC=IBJnEGGdN1WJkouQKElLq7PPzpRy%2BtarlfGJYPosnpPsknjVh8hr2abqND59o9aHe%2FpuqydjAY0mae7g8YWIFT0vuaY4yI3nc4yeOx6bj7hTURvIspP3UUbg3v7%2FMw%3D%3D; _ga_PJFPLJP2TM=GS2.1.s1749716172$o4$g1$t1749716329$j60$l0$h0; agoda.search.01=SHist=4$68211719$9010$1$1$2$1$0$1747257140$17$0|1$20711$9010$1$1$2$1$0$1749716543$17$0|1$1063$9010$1$1$2$1$0$1749716710$17$0&H=8900|0$68211719; agoda.attr.03=ATItems=1922878$05-19-2025 03:29$9c6545bc-1666-4ddf-9cd0-c70620f2972d|1739459$06-12-2025 15:16$|1922878$06-12-2025 16:34$9c6545bc-1666-4ddf-9cd0-c70620f2972d; agoda.landings=1942345|||kb12fm2xbjnnlqatjpf0f5ng|2025-06-12T15:17:37|True|19----1922878|9c6545bc-1666-4ddf-9cd0-c70620f2972d|CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE|kb12fm2xbjnnlqatjpf0f5ng|2025-06-12T16:34:56|True|20----1922878|9c6545bc-1666-4ddf-9cd0-c70620f2972d|CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE|kb12fm2xbjnnlqatjpf0f5ng|2025-06-12T16:34:56|True|99; agoda.lastclicks=1922878||9c6545bc-1666-4ddf-9cd0-c70620f2972d||2025-06-12T16:34:56||kb12fm2xbjnnlqatjpf0f5ng||{"IsPaid":true,"gclid":"CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE","Type":""}; utag_main=v_id:0196d089833a0016e0e418d5b9df0506f008006700838$_sn:4$_se:1$_ss:1$_st:1749722701029$ses_id:1749720901029%3Bexp-session$_pn:1%3Bexp-session; _gcl_aw=GCL.1749720905.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _ga_T408Z268D2=GS2.1.s1749720905$o7$g1$t1749720905$j60$l0$h1935505518; _ga=GA1.2.696457105.1747255402; _gac_UA-6446424-30=1.1749720908.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _ha_aw=GCL.1749720908.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _hab_aw=GCL.1749720908.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _uetsid=b7a744c0476511f0a7566d08cb141b53|46mprx|2|fwp|0|1989; _gcl_au=1.2.148026638.1747255400; _ga_C07L4VP9DZ=GS2.2.s1749720911$o4$g0$t1749720911$j60$l0$h0; __gads=ID=c7e1c6cbd973c570:T=1747255396:RT=1749720911:S=ALNI_MaTQseKMeSHylaabZ7b82b2EZqZ7A; __gpi=UID=000010ad0aa2a9b2:T=1747255396:RT=1749720911:S=ALNI_MZmY8tVwapTHo69em5pySZsktpkTg; cto_bundle=1ipviV92c3FMTmtmSUM1OEI1bm9GWmhEUEpBYUpPekVCbHQ2Q1d5alVjRHFiTzFWTWppdlc2NmRtN3B1QjBuUzBzOEV6dHJqeFN5VW5GcXFlTkJPTkx5VjhjV2JjTjFyYUFyZldnT21mUThIZWlyMDclMkZTaHElMkIxVHdRRUMxa0F5bXN0aVVMY3dXWW5LeU84YXpqaEhibmpyTnFRJTNEJTNE; deviceId=eb9c7c5c-d9ae-4515-8df2-8311f746d9f8; agoda.consent=NG||2025-06-12 09:35:21Z; forterToken=583aaa81cbdc45adb264dfbf2283ec04_1749720923140__UDF43_24ck_; _uetvid=1255d1a0310411f0b9e191098651552a|1mv4r9j|1749723808940|1|1|bat.bing.com/p/conversions/c/h; t_pp=wpr2cuqagh4lzxmktebs; agoda.analytics=Id=4242643095643895756&Signature=5886981715704138779&',
            'Accept' => 'application/json',
        ])->get('https://partners.agoda.com/HotelSuggest/GetSuggestions', [
            'type' => 1,
            'limit' => 10,
            'term' => $term,
        ]);

        return response()->json($suggestionResponse->json());
    }







    public function search(Request $request)
{
    $term = $request->input('term');

    if (empty($term)) {
        return response()->json([]);
    }

    try {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/91.0.4472.124 Safari/537.36',
            'Cookie' => 'agoda.user.03=UserId=cfd674be-8057-4d6f-822b-c7bf6e0f5c81; agoda.prius=PriusID=0&PointsMaxTraffic=Agoda; agoda.price.01=PriceView=1; _fbp=fb.1.1747255400191.110289736142583136; FPID=FPID2.2.3fNFiFLhlwxGUX3tl8ReET22KM9%2B%2FPZu9l%2BsCoVmunM%3D.1747255402; agoda_ptnr_tracking=73c2d7f7-c987-42f7-8fc5-59d832f9517b; ai_user=AZS7E3QxNyALZTyevoYQd6|2025-05-14T20:44:22.442Z; agoda.version.03=CookieId=407a6398-48f3-4068-8eb3-d65cd0b41b8c&DLang=en-us&CurLabel=USD; agoda.familyMode=Mode=0; __RequestVerificationToken=ql40Y0A_J3s9AK5t_dLhoz383AuKI58SQAqnqcpbLIac0XF6Uwsksb0qu0PxN4WNJQ6ROuQnf19sfn4_dq3d0ZxOaoU1; _gid=GA1.2.890101482.1749716159; ul.session=7e7d4ccf-4579-4504-b2b0-9cc7f0e06e9c; ASP.NET_SessionId=gat1js5rtrwpqdsw2tgd43m3; xsrf_token=CfDJ8Dkuqwv-0VhLoFfD8dw7lYzvlw_S31Yy07x5NBPLTUrWCJL9CS4GMyYOrOWCKbZr3m6J9thqkSnXe-fDI49jnSpPUpfbmzrXzASMTMeSzBGAxoj3Vb0gzx7ycnMOr4zLURZoHdHvYp0BHavsZbmB6jk; ul.pc.token=eyJhbGciOiJFUzI1NiJ9.eyJtIjo0MjYzMDk5MjMsInIiOlsiMTI3XzM0ODM4NSJdLCJlIjoiNWRpQCpBSG9AaGQ7L0ZFbkgrMCZSPGAlMTpMKTcyXCJmVWYxMzBOcVpiblxcSS5Ea1pvS2tSTk8wUl1QS0A0QXNVX0s8VzhaalhSXFxybk9FaEoiLCJzcmMiOiJzcmMiLCJzdWIiOiJRaHJZakduZFJpR2lOVnFlbWFOVEl3IiwianRpIjoic1VqNEJ1SV9SNjZZLXVLZFNPSEYyQSIsImlhdCI6MTc0OTcxNjE3OSwiZXhwIjoxNzU3NDkyMTc5LCJ3bHQiOiJmMWE1OTA1Zi05NjIwLTQ1ZTUtOWQ5MS1kMjUxYzA3ZTBiNDIiLCJzIjoyfQ.cB3OvEXFhsFLjsPZ6kbfEQSCG8y65_c8XA2izK5t6q20B-blWP4qcpmn4efYT2YPG2Q7PPUxfm-Rk3E7yoUlYQ; agoda.firstclicks=1942345||||2025-06-12T15:17:37||kb12fm2xbjnnlqatjpf0f5ng||{"IsPaid":true,"gclid":"","Type":""}; ASP.NET_SessionId=kb12fm2xbjnnlqatjpf0f5ng; tealiumEnable=true; FPLC=IBJnEGGdN1WJkouQKElLq7PPzpRy%2BtarlfGJYPosnpPsknjVh8hr2abqND59o9aHe%2FpuqydjAY0mae7g8YWIFT0vuaY4yI3nc4yeOx6bj7hTURvIspP3UUbg3v7%2FMw%3D%3D; _ga_PJFPLJP2TM=GS2.1.s1749716172$o4$g1$t1749716329$j60$l0$h0; agoda.search.01=SHist=4$68211719$9010$1$1$2$1$0$1747257140$17$0|1$20711$9010$1$1$2$1$0$1749716543$17$0|1$1063$9010$1$1$2$1$0$1749716710$17$0&H=8900|0$68211719; agoda.attr.03=ATItems=1922878$05-19-2025 03:29$9c6545bc-1666-4ddf-9cd0-c70620f2972d|1739459$06-12-2025 15:16$|1922878$06-12-2025 16:34$9c6545bc-1666-4ddf-9cd0-c70620f2972d; agoda.landings=1942345|||kb12fm2xbjnnlqatjpf0f5ng|2025-06-12T15:17:37|True|19----1922878|9c6545bc-1666-4ddf-9cd0-c70620f2972d|CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE|kb12fm2xbjnnlqatjpf0f5ng|2025-06-12T16:34:56|True|20----1922878|9c6545bc-1666-4ddf-9cd0-c70620f2972d|CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE|kb12fm2xbjnnlqatjpf0f5ng|2025-06-12T16:34:56|True|99; agoda.lastclicks=1922878||9c6545bc-1666-4ddf-9cd0-c70620f2972d||2025-06-12T16:34:56||kb12fm2xbjnnlqatjpf0f5ng||{"IsPaid":true,"gclid":"CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE","Type":""}; utag_main=v_id:0196d089833a0016e0e418d5b9df0506f008006700838$_sn:4$_se:1$_ss:1$_st:1749722701029$ses_id:1749720901029%3Bexp-session$_pn:1%3Bexp-session; _gcl_aw=GCL.1749720905.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _ga_T408Z268D2=GS2.1.s1749720905$o7$g1$t1749720905$j60$l0$h1935505518; _ga=GA1.2.696457105.1747255402; _gac_UA-6446424-30=1.1749720908.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _ha_aw=GCL.1749720908.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _hab_aw=GCL.1749720908.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _uetsid=b7a744c0476511f0a7566d08cb141b53|46mprx|2|fwp|0|1989; _gcl_au=1.2.148026638.1747255400; _ga_C07L4VP9DZ=GS2.2.s1749720911$o4$g0$t1749720911$j60$l0$h0; __gads=ID=c7e1c6cbd973c570:T=1747255396:RT=1749720911:S=ALNI_MaTQseKMeSHylaabZ7b82b2EZqZ7A; __gpi=UID=000010ad0aa2a9b2:T=1747255396:RT=1749720911:S=ALNI_MZmY8tVwapTHo69em5pySZsktpkTg; cto_bundle=1ipviV92c3FMTmtmSUM1OEI1bm9GWmhEUEpBYUpPekVCbHQ2Q1d5alVjRHFiTzFWTWppdlc2NmRtN3B1QjBuUzBzOEV6dHJqeFN5VW5GcXFlTkJPTkx5VjhjV2JjTjFyYUFyZldnT21mUThIZWlyMDclMkZTaHElMkIxVHdRRUMxa0F5bXN0aVVMY3dXWW5LeU84YXpqaEhibmpyTnFRJTNEJTNE; deviceId=eb9c7c5c-d9ae-4515-8df2-8311f746d9f8; agoda.consent=NG||2025-06-12 09:35:21Z; forterToken=583aaa81cbdc45adb264dfbf2283ec04_1749720923140__UDF43_24ck_; _uetvid=1255d1a0310411f0b9e191098651552a|1mv4r9j|1749723808940|1|1|bat.bing.com/p/conversions/c/h; t_pp=wpr2cuqagh4lzxmktebs; agoda.analytics=Id=4242643095643895756&Signature=5886981715704138779&',
            'Accept' => 'application/json',
        ])->timeout(10) // Prevent long hangs
          ->retry(2, 200) // Retry on transient errors
          ->withOptions([
              'verify' => false, // use with caution, only for dev
              'curl' => [
                  CURLOPT_FOLLOWLOCATION => true,
              ],
          ])
          ->get('https://partners.agoda.com/HotelSuggest/GetSuggestions', [
              'type' => 1,
              'limit' => 10,
              'term' => $term,
          ]);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Unable to contact Agoda API',
            'message' => $e->getMessage()
        ], 500);
    }
}
}
