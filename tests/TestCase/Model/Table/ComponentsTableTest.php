<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ComponentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ComponentsTable Test Case
 */
class ComponentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ComponentsTable
     */
    public $Components;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Components',
        'app.Workouts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Components') ? [] : ['className' => ComponentsTable::class];
        $this->Components = TableRegistry::getTableLocator()->get('Components', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Components);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
