<?php

namespace destvil\cbr\Control;

use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Fields\ExpressionField;
use destvil\cbr\Boundary\Exceptions\CbrException;
use destvil\cbr\Entity\Currency;
use destvil\cbr\ObjectValues\CurrencyNavigationFilter;

class CurrencyRepository
{
    /**
     * @param array $data
     * @return AddResult
     * @throws CbrException
     */
    public function create(array $data): AddResult
    {
        $result = CurrencyTable::add($data);
        if ($result->isSuccess()) {
            return $result;
        }

        throw new CbrException(implode(',', $result->getErrorMessages()));
    }

    /**
     * @param string $code
     * @param array $data
     * @return UpdateResult
     * @throws CbrException
     */
    public function update(string $code, array $data): UpdateResult
    {
        $result = CurrencyTable::update($code, $data);
        if ($result->isSuccess()) {
            return $result;
        }

        throw new CbrException(implode(',', $result->getErrorMessages()));
    }

    /**
     * @param string $code
     * @return DeleteResult
     * @throws CbrException
     */
    public function delete(string $code): DeleteResult
    {
        $result = CurrencyTable::delete($code);
        if ($result->isSuccess()) {
            return $result;
        }

        throw new CbrException(implode(',', $result->getErrorMessages()));
    }

    /**
     * @return Currency[]
     * @throws \Exception
     */
    public function findAll(): array
    {
        $result = CurrencyTable::query()
            ->setSelect(['code', 'date', 'course'])
            ->exec();

        $currencies = [];
        while ($item = $result->fetch()) {
            $currencies[] = $this->fromArray($item);
        }

        return $currencies;
    }

    /**
     * @param CurrencyNavigationFilter $navigationFilter
     * @return Currency[]
     * @throws \Exception
     */
    public function findByNav(CurrencyNavigationFilter $navigationFilter): array
    {
        $query = CurrencyTable::query()
            ->setSelect(['code', 'date', 'course']);

        foreach ($navigationFilter->getOrder() as $field => $order) {
            $query->addOrder($field, $order);
        }

        $query->setLimit($navigationFilter->getLimit());
        $query->setOffset($navigationFilter->getOffset());

        $result = $query->exec();

        $currencies = [];
        while ($item = $result->fetch()) {
            $currencies[] = $this->fromArray($item);
        }

        return $currencies;
    }

    public function getCount(): int
    {
        return (int) CurrencyTable::query()
            ->setSelect(['CNT'])
            ->registerRuntimeField(new ExpressionField('CNT', 'COUNT(code)'))
            ->exec()
            ->fetch()['CNT'];
    }

    private function fromArray(array $data): Currency
    {
        return new Currency(
            $data['code'],
            $data['course'],
            $data['date']
        );
    }
}