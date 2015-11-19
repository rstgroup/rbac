<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace RbacTest;

use PHPUnit_Framework_TestCase as TestCase;
use Rbac\Exception\RuntimeException;
use Rbac\Rbac;
use Rbac\Role\HierarchicalRole;
use Rbac\Role\Role;

/**
 * @covers Rbac\Rbac
 * @group  Coverage
 */
class RbacTest extends TestCase
{
    /**
     * @covers Rbac\Rbac::isGranted
     */
    public function testEnforcePermissionAsString()
    {
        $this->setExpectedException('Rbac\Exception\RuntimeException');

        $rbac = new Rbac();
        $rbac->isGranted(array(), new \stdClass());
    }

    /**
     * @covers Rbac\Rbac::isGranted
     */
    public function testCanConvertSingleRole()
    {
        $role = new Role('Foo');
        $role->addPermission('permission');

        $rbac = new Rbac();

        $this->assertTrue($rbac->isGranted($role, 'permission'));
    }

    /**
     * @covers Rbac\Rbac::isGranted
     */
    public function testCanUseEmptyArray()
    {
        $rbac = new Rbac();
        $this->assertFalse($rbac->isGranted(array(), 'permission'));
    }

    /**
     * @covers Rbac\Rbac::isGranted
     */
    public function testCanCheckMultipleRolesWithMatchingPermission()
    {
        $role1 = new Role('Foo');

        $role2 = new Role('Bar');
        $role2->addPermission('permission');

        $roles = array($role1, $role2);
        $rbac  = new Rbac();

        $this->assertTrue($rbac->isGranted($roles, 'permission'));
    }

    /**
     * @covers Rbac\Rbac::isGranted
     */
    public function testReturnFalseIfNoRoleHasPermission()
    {
        $role1 = new Role('Foo');
        $role2 = new Role('Bar');

        $roles = array($role1, $role2);
        $rbac  = new Rbac();

        $this->assertFalse($rbac->isGranted($roles, 'permission'));
    }

    /**
     * @covers Rbac\Rbac::isGranted
     */
    public function testCanCheckHierarchicalRole()
    {
        $childRole  = new Role('Bar');
        $childRole->addPermission('permission');

        $parentRole = new HierarchicalRole('Foo');
        $parentRole->addChild($childRole);

        $rbac = new Rbac();

        $this->assertTrue($rbac->isGranted($parentRole, 'permission'));
    }

    /**
     * @covers Rbac\Rbac::isGranted
     */
    public function testReturnFalseIfNoHierarchicalRoleHasPermission()
    {
        $childRole  = new Role('Bar');

        $parentRole = new HierarchicalRole('Foo');
        $parentRole->addChild($childRole);

        $rbac = new Rbac();

        $this->assertFalse($rbac->isGranted($parentRole, 'permission'));
    }
}
