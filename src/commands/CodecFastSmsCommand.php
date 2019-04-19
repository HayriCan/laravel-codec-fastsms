<?php

namespace HayriCan\CodecFastSms\Commands;

use HayriCan\CodecFastSms\Models\Sms;
use Illuminate\Console\Command;

class CodecFastSmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fastsms:send
                            {--phone= : Phone number(s) to send multiple numbers you should put `~` between numbers}
                            {--messageContent= : Message content(s) if you want to send different messages to different numbers you should put `~` between message contents}
                            {--msgSpecialId= : This field use for searching on the Codec system records}
                            {--headerCode= : You can use this field for tracking messages from Codec Customer Service}
                            {--optionalParameters= : Optional parameters}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command sends sms from Codec Fast Sms API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $phone = $this->option('phone');
        $messageContent = $this->option('messageContent');
        if ($phone && $messageContent){
            $phoneCount = count(explode('~',$phone));
            $msgCount = count(explode('~',$messageContent));

            if ($phoneCount == $msgCount && $phoneCount == 1){
                $isOtn  = "true";
            }elseif ($phoneCount == $msgCount && $phoneCount > 1){
                $isOtn = "false";
            }elseif ($phoneCount > $msgCount && $msgCount == 1){
                $isOtn  = "true";
            }else{
                $this->error('Numara Sayısı İle Mesaj Sayısı Eşleşmiyor!');
                return false;
            }

            $input_msgSpecialId = $this->option('msgSpecialId');
            $input_headerCode = $this->option('headerCode');
            $input_optionalParameters = $this->option('optionalParameters');

            if ($input_msgSpecialId){
                $msgSpecialId = $input_msgSpecialId;
            }else{
                $msgSpecialId = "Empty";
            }

            if ($input_headerCode){
                $headerCode = $input_headerCode;
            }else{
                $headerCode = "Empty";
            }

            if ($input_optionalParameters){
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
                    $this->error('Veritabanı Kayıt Yaparken Hata İle Karşılaşıldı');
                    return false;
                }
            }else{
                $smsSend = $this->smsSend($phone,$messageContent,$msgSpecialId,$headerCode,$isOtn,$optionalParameters);
                $this->info($smsSend);
                return true;
            }
        }
        $this->error('Phone ve MessageContent değerleri boş olamaz!');
        return false;
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
            $this->error('SMS Servis Sonucu XML Dönmedi');
            return false;
        } else {
            return $xml;
        }
    }
}
