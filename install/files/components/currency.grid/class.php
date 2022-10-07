<?php

defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Grid\Options;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use destvil\cbr\Control\CurrencyRepository;
use \destvil\cbr\Entity\Currency;
use destvil\cbr\ObjectValues\CurrencyNavigationFilter;

class CurrencyGridComponent extends CBitrixComponent
{
    const GRID_ID = 'currency_grid';
    const DEFAULT_PAGE_SIZE = 10;

    private Options $options;
    private CurrencyRepository $currencyRep;

    public function executeComponent()
    {
        Loader::includeModule('destvil.cbr');

        $this->options = new Options(self::GRID_ID);
        $this->currencyRep = new CurrencyRepository();

        $gridOptionsNav = $this->options->GetNavParams([
            'nPageSize' => $this->options->getCurrentOptions()['page_size'] ?: static::DEFAULT_PAGE_SIZE
        ]);

        $currentPage = $this->request->offsetExists('page') ? $this->request->get('page') : 1;

        $navigation = new PageNavigation(self::GRID_ID);
        $navigation
            ->allowAllRecords(true)
            ->setPageSize($gridOptionsNav['nPageSize'])
            ->setRecordCount($this->getCurrenciesCount())
            ->setCurrentPage($currentPage > 0 ? $currentPage : $navigation->getPageCount())
            ->initFromUri();

        $this->arResult = [
            'GRID_ID' => self::GRID_ID,
            'HEADERS' => $this->getHeaders(),
            'NAV_OBJECT' => $navigation,
            'ROWS' => $this->getRows($navigation),
            'SHOW_PAGESIZE' => true,
            'PAGE_SIZES' => [
                ['NAME' => '5', 'VALUE' => '5'],
                ['NAME' => '10', 'VALUE' => '10'],
            ],
            'SHOW_CHECK_ALL_CHECKBOXES' => false,
            'SHOW_ROW_CHECKBOXES' => false,
            'AJAX_MODE' => 'Y'
        ];

        $this->includeComponentTemplate();
    }

    private function getHeaders(): array
    {
        return [
            [
                'id' => 'code',
                'type' => 'text',
                'name' => Loc::getMessage('CODE_COLUMN_NAME'),
                'sort' => 'code',
                'default' => true
            ],
            [
                'id' => 'date',
                'type' => 'date',
                'name' => Loc::getMessage('DATE_COLUMN_NAME'),
                'sort' => 'date',
                'default' => true
            ],
            [
                'id' => 'course',
                'type' => 'number',
                'name' => Loc::getMessage('COURSE_COLUMN_NAME'),
                'sort' => 'course',
                'default' => true
            ]
        ];
    }

    private function getRows(PageNavigation $navigation): array
    {
        $rows = [];

        foreach ($this->getCurrencies($navigation) as $currency) {
            $rows[] = [
                'data' => $currency->toArray()
            ];
        }

        return $rows;
    }

    /**
     * @param PageNavigation $navigation
     * @return Currency[]
     * @throws Exception
     */
    private function getCurrencies(PageNavigation $navigation): array
    {
        $currencyNavigationFilter = $this->buildCurrencyNavigationFilter($navigation);
        return $this->currencyRep->findByNav($currencyNavigationFilter);
    }

    private function getCurrenciesCount(): int
    {
        return $this->currencyRep->getCount();
    }

    private function buildCurrencyNavigationFilter(PageNavigation $navigation): CurrencyNavigationFilter
    {
        $currencyNavigationFilter = new CurrencyNavigationFilter();

        foreach ($this->options->getSorting()['sort'] as $field => $order) {
            $currencyNavigationFilter->order($field, $order);
        }

        $currencyNavigationFilter->setLimit($navigation->getLimit());
        $currencyNavigationFilter->setOffset($navigation->getOffset());

        return $currencyNavigationFilter;
    }
}