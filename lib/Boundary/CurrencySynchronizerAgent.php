<?php

namespace destvil\cbr\Boundary;

use Bitrix\Main\Type\DateTime;
use destvil\cbr\Boundary\Exceptions\ArrayTypeMismatchException;
use destvil\cbr\Boundary\Exceptions\CbrException;
use destvil\cbr\Control\CbrSoapClient;
use destvil\cbr\Control\CurrencyRepository;
use destvil\cbr\Entity\CbrCurrency;
use destvil\cbr\Entity\Currency;

class CurrencySynchronizerAgent
{
    private CbrSoapClient $cbrClient;
    private CurrencyRepository $currencyRep;

    public function __construct(
        ?CbrSoapClient $cbrClient = null,
        ?CurrencyRepository $currencyRep = null
    )
    {
        $this->cbrClient = $cbrClient ?? new CbrSoapClient();
        $this->currencyRep = $currencyRep ?? new CurrencyRepository();
    }

    public static function execute(): string
    {
        try {
            (new CurrencySynchronizerAgent())->synchronize();

        } catch (\Throwable $exception) {
            \CEventLog::Add([
                'SEVERITY' => 'ERROR',
                'AUDIT_TYPE_ID' => 'CURRENCY_SYNCHRONIZE_ERROR',
                'MODULE_ID' => 'destvil.cbr',
                'DESCRIPTION' => $exception->getMessage()
            ]);

        } finally {
            return __METHOD__ . '();';
        }
    }

    /**
     * @return void
     * @throws ArrayTypeMismatchException
     * @throws CbrException
     * @throws \Exception
     */
    private function synchronize(): void
    {
        $localCurrencies = $this->getLocalCurrencies();
        $remoteCurrencies = $this->getRemoteCurrencies();

        $newCurrencies = array_diff_key($remoteCurrencies, $localCurrencies);
        $this->appendNewCurrencies($newCurrencies);

        $updatedCurrencies = array_intersect_key($remoteCurrencies, $localCurrencies);
        $this->updateExistsCurrencies($updatedCurrencies);

        $oldCurrencies = array_diff_key($localCurrencies, $remoteCurrencies);
        $this->deleteOldCurrencies($oldCurrencies);
    }

    /**
     * @return Currency[]
     * @throws \Exception
     */
    private function getLocalCurrencies(): array
    {
        $localCurrencies = [];
        foreach ($this->currencyRep->findAll() as $currency) {
            $localCurrencies[$currency->getCode()] = $currency;
        }

        return $localCurrencies;
    }

    /**
     * @return CbrCurrency[]
     * @throws \Exception
     */
    private function getRemoteCurrencies(): array
    {
        $remoteCurrencies = [];
        foreach ($this->cbrClient->getCursOnDate(new DateTime()) as $cbrCurrency) {
            $remoteCurrencies[$cbrCurrency->getCode()] = $cbrCurrency;
        }

        return $remoteCurrencies;
    }

    /**
     * @param array $currencies
     * @return void
     * @throws ArrayTypeMismatchException
     * @throws CbrException
     */
    private function appendNewCurrencies(array $currencies): void
    {
        foreach ($currencies as $currency) {
            if (!$currency instanceof CbrCurrency) {
                throw new ArrayTypeMismatchException(CbrCurrency::class);
            }

            $this->currencyRep->create([
                'code' => $currency->getCode(),
                'course' => $currency->getCourse()
            ]);
        }
    }

    /**
     * @param CbrCurrency[] $currencies
     * @return void
     * @throws ArrayTypeMismatchException
     * @throws CbrException
     */
    private function updateExistsCurrencies(array $currencies): void
    {
        foreach ($currencies as $currency) {
            if (!$currency instanceof CbrCurrency) {
                throw new ArrayTypeMismatchException(CbrCurrency::class);
            }

            $this->currencyRep->update($currency->getCode(), [
                'course' => $currency->getCourse()
            ]);
        }
    }

    /**
     * @param Currency[] $currencies
     * @return void
     * @throws ArrayTypeMismatchException
     * @throws CbrException
     */
    private function deleteOldCurrencies(array $currencies): void
    {
        foreach ($currencies as $currency) {
            if (!$currency instanceof Currency) {
                throw new ArrayTypeMismatchException(Currency::class);
            }

            $this->currencyRep->delete($currency->getCode());
        }
    }
}