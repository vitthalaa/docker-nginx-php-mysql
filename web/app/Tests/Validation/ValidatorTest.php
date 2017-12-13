<?php
namespace Acme\Tests\Validation;

use Acme\Http\Request;
use Acme\Http\Response;
use Acme\Http\Session;
use Acme\Validation\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var Response|\PHPUnit_Framework_MockObject_MockObject
     */
    private $response;

    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $session;

    protected function setUp()
    {
        parent::setUp();

        $this->response = $this->createMock(Response::class);
        $this->session  = $this->createMock(Session::class);
        $this->request  = $this->createMock(Request::class);
    }

    public function testGetIsValidReturnsTrue()
    {
        $validator = $this->getValidator();

        $validator->setIsValid(true);

        $this->assertTrue($validator->getIsValid());
    }

    public function testGetIsValidReturnsFalse()
    {
        $validator = $this->getValidator();
        $validator->setIsValid(false);

        $this->assertFalse($validator->getIsValid());
    }

    public function testCheckForMinStringLengthWithValidData()
    {
        $this->setRequest('some_value');

        $validator = $this->getValidator();
        $errors    = $validator->check(['mintype' => 'min:3']);

        $this->assertCount(0, $errors);
    }

    public function testCheckForMinStringLengthWithInValidData()
    {
        $this->setRequest('s');

        $validator = $this->getValidator();
        $errors    = $validator->check(['mintype' => 'min:3']);

        $this->assertCount(1, $errors);
        $this->assertStringMatchesFormat('%s must be at least %d characters long!', $errors[0]);
    }

    public function testCheckForEmailWithValidData()
    {
        $this->setRequest('test@test.com');

        $validator = $this->getValidator();
        $errors    = $validator->check(['email' => 'email']);

        $this->assertCount(0, $errors);
    }

    public function testCheckForEmailWithInValidData()
    {
        $this->setRequest('test@test');

        $validator = $this->getValidator();
        $errors    = $validator->check(['email' => 'email']);

        $this->assertCount(1, $errors);
        $this->assertStringMatchesFormat('%s must be a valid email!', $errors[0]);
    }

    public function testCheckEqualToWithValidData()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->willReturn('Jack');

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->willReturn('Jack');

        $validator = $this->getValidator();
        $errors    = $validator->check(['field1' => 'equalTo:field2']);

        $this->assertCount(0, $errors);
    }

    public function testCheckEqualToWithInValidData()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->willReturn('Jack');

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->willReturn('jack');

        $validator = $this->getValidator();
        $errors    = $validator->check(['field1' => 'equalTo:field2']);

        $this->assertCount(1, $errors);
        $this->assertEquals('Value does not match verification value!', $errors[0]);
    }

    public function testCheckUniqueWithValidData()
    {
        $validator = $this->getMockBuilder(Validator::class)
            ->setConstructorArgs([$this->request, $this->response, $this->session])
            ->setMethods(['getRows'])
            ->getMock();

        $validator->method('getRows')
            ->willReturn([]);

        $result = $validator->check(['field' => 'unique:User']);

        $this->assertCount(0, $result);
    }

    public function testCheckUniqueWithInValidData()
    {
        $validator = $this->getMockBuilder(Validator::class)
            ->setConstructorArgs([$this->request, $this->response, $this->session])
            ->setMethods(['getRows'])
            ->getMock();

        $validator->method('getRows')
            ->willReturn(['some_value']);

        $result = $validator->check(['field1' => 'unique:User']);

        $this->assertCount(1, $result);
        $this->assertStringEndsWith("already exists in this system!", $result[0]);
    }

    public function testValidateWithNoErrors()
    {
        $validator = $this->getMockBuilder(Validator::class)
            ->setConstructorArgs([$this->request, $this->response, $this->session])
            ->setMethods(['check'])
            ->getMock();

        $validator->method('check')
            ->willReturn([]);

        $result = $validator->validate(['email' => 'email'], '/errors');

        $this->assertTrue($result);
    }

    public function testValidateWithErrors()
    {
        $validator = $this->getMockBuilder(Validator::class)
            ->setConstructorArgs([$this->request, $this->response, $this->session])
            ->setMethods(['check', 'redirectToPage'])
            ->getMock();

        $validator->method('check')
            ->willReturn(['some error message']);

        $validator->expects($this->once())
            ->method('redirectToPage');

        $validator->validate(['email' => 'email'], '/errors');
    }

    private function setRequest($input)
    {
        $this->request->expects(static::once())
            ->method('input')
            ->willReturn($input);
    }

    /**
     * @return Validator
     */
    private function getValidator()
    {
        return new Validator($this->request, $this->response, $this->session);
    }
}