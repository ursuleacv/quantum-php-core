<?php

namespace Quantum\Services {

    use Quantum\Mvc\Qt_Service;

    class TestService extends Qt_Service
    {

        public static $count = 0;

        public function __init()
        {
            self::$count++;
        }

    }

}

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\ServiceException;
    use Quantum\Factory\ServiceFactory;
    use Quantum\Services\TestService;

    class ServiceFactoryTest extends TestCase
    {

        private $serviceFactory;

        public function setUp(): void
        {
            $this->helperMock = Mockery::mock('overload:Quantum\Helpers\Helper');

            $this->helperMock->shouldReceive('_message')->andReturnUsing(function($subject, $params) {
                if (is_array($params)) {
                    return preg_replace_callback('/{%\d+}/', function () use (&$params) {
                        return array_shift($params);
                    }, $subject);
                } else {
                    return preg_replace('/{%\d+}/', $params, $subject);
                }
            });

            $this->serviceFactory = new ServiceFactory();
        }

        public function tearDown(): void
        {
            $reflectionProperty = new \ReflectionProperty(ServiceFactory::class, 'initialized');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue(ServiceFactory::class, []);

            TestService::$count = 0;
        }

        public function testServiceGet()
        {
            $service = $this->serviceFactory->get(TestService::class);

            $this->assertInstanceOf('Quantum\Mvc\Qt_Service', $service);

            $this->assertInstanceOf('Quantum\Services\TestService', $service);
        }

        public function testServiceGetAndInit()
        {
            /* Calling 3 tiems to verify __init() method works only once */

            $this->serviceFactory->get(TestService::class);

            $this->assertEquals(1, TestService::$count);

            $this->serviceFactory->get(TestService::class);

            $this->assertEquals(1, TestService::$count);

            $this->serviceFactory->get(TestService::class);

            $this->assertEquals(1, TestService::$count);
        }

        public function testServiceProxy()
        {
            $service = $this->serviceFactory->proxy(TestService::class);

            $this->assertInstanceOf('Quantum\Factory\ServiceFactory', $service);
        }

        public function testServiceProxyAndInit()
        {
            /* Calling 3 tiems to verify __init() method works only once */

            $this->serviceFactory->proxy(TestService::class);

            $this->assertEquals(1, TestService::$count);

            $this->serviceFactory->proxy(TestService::class);

            $this->assertEquals(1, TestService::$count);

            $this->serviceFactory->proxy(TestService::class);

            $this->assertEquals(1, TestService::$count);
        }

        public function testServiceCreate()
        {
            $service = $this->serviceFactory->create(TestService::class);

            $this->assertInstanceOf('Quantum\Mvc\Qt_Service', $service);

            $this->assertInstanceOf('Quantum\Services\TestService', $service);
        }

        public function testServiceCreateAndInit()
        {
            /* Calling 3 tiems to verify __init() method works each time */

            $this->serviceFactory->create(TestService::class);

            $this->assertEquals(1, TestService::$count);

            $this->serviceFactory->create(TestService::class);

            $this->assertEquals(2, TestService::$count);

            $this->serviceFactory->create(TestService::class);

            $this->assertEquals(3, TestService::$count);
        }

        public function testServiceNotFound()
        {
            $this->expectException(ServiceException::class);

            $this->expectExceptionMessage('Service `NonExistentClass` not found');

            $this->serviceFactory->get(\NonExistentClass::class);
        }

        public function testServiceNotInstanceOfQtService()
        {
            $this->expectException(ServiceException::class);

            $this->expectExceptionMessage('Service `Mockery\Undefined` is not instance of `Quantum\Mvc\Qt_Service`');

            $this->serviceFactory->get(\Mockery\Undefined::class);
        }

    }

}
