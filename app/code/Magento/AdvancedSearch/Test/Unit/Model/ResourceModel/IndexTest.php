<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdvancedSearch\Test\Unit\Model\ResourceModel;

use Magento\AdvancedSearch\Model\ResourceModel\Index;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

class IndexTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Index
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceContextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataPoolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $adapterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceConnectionMock;

    protected function setUp()
    {
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->resourceContextMock = $this->createMock(Context::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->resourceContextMock->expects($this->any())
            ->method('getResources')
            ->willReturn($this->resourceConnectionMock);
        $this->adapterMock = $this->createMock(AdapterInterface::class);
        $this->resourceConnectionMock->expects($this->any())->method('getConnection')->willReturn($this->adapterMock);
        $this->metadataPoolMock = $this->createMock(MetadataPool::class);

        $this->model = new Index(
            $this->resourceContextMock,
            $this->storeManagerMock,
            $this->metadataPoolMock
        );
    }

    public function testGetPriceIndexDataUsesFrontendPriceIndexerTable()
    {
        $storeId = 1;
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())->method('getId')->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())->method('getStore')->with($storeId)->willReturn($storeMock);

        $selectMock = $this->createMock(Select::class);
        $selectMock->expects($this->any())->method('from')->willReturnSelf();
        $selectMock->expects($this->any())->method('where')->willReturnSelf();
        $this->adapterMock->expects($this->once())->method('select')->willReturn($selectMock);
        $this->adapterMock->expects($this->once())->method('fetchAll')->with($selectMock)->willReturn([]);

        $this->assertEmpty($this->model->getPriceIndexData([1], $storeId));
    }
}