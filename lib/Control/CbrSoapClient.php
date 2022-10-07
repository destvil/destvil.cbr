<?php

namespace destvil\cbr\Control;

use Bitrix\Main\Type\DateTime;
use destvil\cbr\Entity\CbrCurrency;
use SoapClient;

class CbrSoapClient
{
    private SoapClient $client;

    public function __construct()
    {
        $this->client = new SoapClient('http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL');
    }

    /**
     * @param DateTime $dateTime
     * @return CbrCurrency[]
     * @throws \Exception
     */
    public function getCursOnDate(DateTime $dateTime): array
    {
        $response = $this->client->GetCursOnDate([
            'On_date' => $dateTime->format(DATE_RFC3339)
        ]);

        $simpleXmlElement = new \SimpleXMLElement($response->GetCursOnDateResult->any);

        $currencies = [];
        foreach ($simpleXmlElement->ValuteData->ValuteCursOnDate as $currency) {
            $currencies[] = new CbrCurrency(
                (string) $currency->VchCode,
                (string) $currency->Vname,
                (float) $currency->Vcurs
            );
        }

        return $currencies;
    }
}