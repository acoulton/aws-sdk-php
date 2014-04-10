<?php
namespace Aws\Test\Api\ErrorParser;

use Aws\Api\ErrorParser\JsonRestErrorParser;
use GuzzleHttp\Message\MessageFactory;

/**
 * @covers Aws\Api\ErrorParser\JsonRestErrorParser
 */
class JsonRestErrorParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParsesClientErrorResponses()
    {
        $response = (new MessageFactory())->fromMessage(
            "HTTP/1.1 400 Bad Request\r\n" .
            "x-amzn-requestid: xyz\r\n\r\n" .
            '{ "type": "client", "message": "lorem ipsum", "code": "foo" }'
        );

        $parser = new JsonRestErrorParser();
        $this->assertEquals(array(
            'code'       => 'foo',
            'message'    => 'lorem ipsum',
            'type'       => 'client',
            'request_id' => 'xyz',
            'parsed'     => array(
                'type'    => 'client',
                'message' => 'lorem ipsum',
                'code'    => 'foo'
            )
        ), $parser($response));
    }

    public function testParsesClientErrorResponseWithCodeInHeader()
    {
        $response = (new MessageFactory())->fromMessage(
            "HTTP/1.1 400 Bad Request\r\n" .
            "x-amzn-RequestId: xyz\r\n" .
            "x-amzn-ErrorType: foo:bar\r\n\r\n" .
            '{"message": "lorem ipsum"}'
        );

        $parser = new JsonRestErrorParser();
        $this->assertEquals(array(
            'code'       => 'foo',
            'message'    => 'lorem ipsum',
            'type'       => 'client',
            'request_id' => 'xyz',
            'parsed'     => array(
                'message' => 'lorem ipsum',
            )
        ), $parser($response));
    }
}
