<?php

namespace HayriCan\CodecFastSms\Controllers;

use App\Http\Controllers\Controller;
use HayriCan\CodecFastSms\Models\Sms;
use Illuminate\Http\Request;

class CodecFastSmsController extends Controller
{


    public function postSmsVariables(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required',
            'messageContent' => 'required'
        ]);
        $phone = $request->input('phone');
        $messageContent = $request->input('messageContent');

        $phoneCount = count(explode('~',$phone));
        $msgCount = count(explode('~',$messageContent));

        if ($phoneCount == $msgCount && $phoneCount == 1){
            $isOtn  = "true";
        }elseif ($phoneCount == $msgCount && $phoneCount > 1){
            $isOtn = "false";
        }elseif ($phoneCount > $msgCount && $msgCount == 1){
            $isOtn  = "true";
        }else{
            return response()->json(['error'=>'Numara Sayısı İle Mesaj Sayısı Eşleşmiyor'],402,[],JSON_UNESCAPED_UNICODE);
        }

        $input_msgSpecialId = $request->input('msgSpecialId');
        $input_headerCode = $request->input('headerCode');
        $input_optionalParameters = $request->input('optionalParameters');
        if (isset($input_msgSpecialId) && !empty($input_msgSpecialId) ){
            $msgSpecialId = $input_msgSpecialId;
        }else{
            $msgSpecialId = "Empty";
        }

        if (isset($input_headerCode) && !empty($input_headerCode)){
            $headerCode = $input_headerCode;
        }else{
            $headerCode = "Empty";
        }

        if (isset($input_optionalParameters) && !empty($input_optionalParameters)){
            $optionalParameters = $input_optionalParameters;
        }else{
            $optionalParameters = "";
        }

        if (config('codecfastsms.record')){
            $sms = new Sms;
            $sms->phone = $phone;
            $sms->messageContent= $messageContent;
            $sms->msgSpecialId = $msgSpecialId;
            $sms->isOtn = ($isOtn == 'true') ? 1:0;
            $sms->headerCode = $headerCode;
            $record = $sms->save();

            if ($record){
                $smsSend = $this->smsSend($phone,$messageContent,$msgSpecialId,$headerCode,$isOtn,$optionalParameters);
                return $smsSend;
            }else{
                return response()->json(['error'=>'Veritabanı Kayıt Yaparken Hata İle Karşılaşıldı'],402,[],JSON_UNESCAPED_UNICODE);
            }
        }else{
            $smsSend = $this->smsSend($phone,$messageContent,$msgSpecialId,$headerCode,$isOtn,$optionalParameters);
            return $smsSend;
        }

    }

    protected function smsSend($phone,$messageContent,$msgSpecialId,$headerCode,$isOtn,$optionalParameters)
    {
        $un=config('codecfastsms.username');
        $pw=config('codecfastsms.password');
        $sender=config('codecfastsms.sender');
        $responseType = 3;
        $url="http://fastsms.codec.com.tr/FastApi.asmx/SendSms?";
        $url.="userName=".$un;
        $url.="&password=".$pw;
        $url.="&optionalParameters=".$optionalParameters;
        $url.="&sender=".$sender;
        $url.="&phone=".$phone;
        $url.="&messageContent=".urlencode($messageContent);
        $url.="&msgSpecialId=".$msgSpecialId;
        $url.="&isOtn=".$isOtn;
        $url.="&headerCode=".$headerCode;
        $url.="&responseType=".$responseType;
        $result = file_get_contents($url);
        $xml=simplexml_load_string($result) or die("Error: Cannot create object");
        if ($xml === false) {
            return response()->json(['error'=>'SMS Servis Sonucu XML Dönmedi'],402,[],JSON_UNESCAPED_UNICODE);
        } else {
            return $xml;
        }
    }

    protected function getCredit()
    {
        $un=config('codecfastsms.username');
        $pw=config('codecfastsms.password');
        $url="http://fastsms.codec.com.tr/FastApi.asmx/GetCredit?";
        $url.="userName=".$un;
        $url.="&password=".$pw;
        $url.="&optionalParameters=null";
        $result = file_get_contents($url);
        $xml=simplexml_load_string($result) or die("Error: Cannot create object");
        if ($xml === false) {
            return response()->json(['error'=>'SMS Servis Sonucu XML Dönmedi'],402,[],JSON_UNESCAPED_UNICODE);
        } else {
            return $xml;
        }
    }

}
