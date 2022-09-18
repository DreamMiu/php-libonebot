<?php

declare(strict_types=1);

namespace Tests\OneBot\V12\Action;

use OneBot\Util\FileUtil;
use OneBot\V12\Action\DefaultActionHandler;
use OneBot\V12\Object\Action;
use OneBot\V12\Object\ActionResponse;
use OneBot\V12\OneBot;
use OneBot\V12\RetCode;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ActionBaseTest extends TestCase
{
    private static $handler;

    public static function setUpBeforeClass(): void
    {
        self::$handler = new DefaultActionHandler();
    }

    public static function tearDownAfterClass(): void
    {
        FileUtil::removeDirRecursive(OneBot::getInstance()->getConfig()->get('file_upload.path'));
    }

    public function testOnDeleteMessage()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onDeleteMessage(new Action('delete_message')));
    }

    public function testOnGetGroupMemberList()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onGetGroupMemberList(new Action('get_group_member_list')));
    }

    public function testOnGetSupportedActions()
    {
        $response = self::$handler->onGetSupportedActions(new Action('get_supported_actions'));
        $this->assertEquals('ok', $response->status);
        $this->assertEquals(0, $response->retcode);
        $this->assertNotEmpty($response->data);
    }

    public function testOnGetSelfInfo()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onGetSelfInfo(new Action('get_self_info')));
    }

    public function testOnGetLatestEvents()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onGetLatestEvents(new Action('get_latest_events')));
    }

    public function testOnGetVersion()
    {
        $this->assertEquals(0, self::$handler->onGetVersion(new Action('get_version'))->retcode);
    }

    public function testOnGetGroupList()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onGetGroupList(new Action('get_group_list')));
    }

    public function testOnGetGroupMemberInfo()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onGetGroupMemberInfo(new Action('get_group_member_info')));
    }

    public function testOnSetGroupName()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onSetGroupName(new Action('set_group_name')));
    }

    public function testOnLeaveGroup()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onLeaveGroup(new Action('leave_group')));
    }

    public function testOnGetStatus()
    {
        $this->assertEquals(0, self::$handler->onGetStatus(new Action('get_status'))->retcode);
    }

    public function testOnGetFriendList()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onGetFriendList(new Action('get_friend_list')));
    }

    public function testOnGetGroupInfo()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onGetGroupInfo(new Action('get_group_info')));
    }

    public function testOnGetUserInfo()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onGetUserInfo(new Action('get_user_info')));
    }

    public function testOnSendMessage()
    {
        $this->assertEquals(ActionResponse::create()->fail(RetCode::UNSUPPORTED_ACTION), self::$handler->onSendMessage(new Action('send_message')));
    }

    public function testOnUploadFileUrl()
    {
        $resp = self::$handler->onUploadFile(new Action('upload_file', [
            'type' => 'url',
            'name' => 'testfile.jpg',
            'url' => 'https://zhamao.xin/file/hello.jpg',
        ]), ONEBOT_JSON);
        $this->assertEquals(RetCode::OK, $resp->retcode);
        $this->assertArrayHasKey('file_id', $resp->data);
        $path = ob_config('file_upload.path', getcwd() . '/data/files');
        [$meta, $content] = FileUtil::getMetaFile($path, $resp->data['file_id']);
        $this->assertEquals('testfile.jpg', $meta['name']);
        $this->assertEquals('https://zhamao.xin/file/hello.jpg', $meta['url']);
        $this->assertNotNull($content);
    }

    public function testOnUploadFilePath()
    {
        $resp = self::$handler->onUploadFile(new Action('upload_file', [
            'type' => 'path',
            'name' => 'a.txt',
            'path' => __FILE__,
        ]), ONEBOT_JSON);
        $this->assertEquals(RetCode::OK, $resp->retcode);
        $this->assertArrayHasKey('file_id', $resp->data);
        $path = ob_config('file_upload.path', getcwd() . '/data/files');
        [$meta, $content] = FileUtil::getMetaFile($path, $resp->data['file_id']);
        $this->assertEquals('a.txt', $meta['name']);
        $this->assertEquals(file_get_contents(__FILE__), $content);
    }

    public function testOnUploadFileData()
    {
        $resp = self::$handler->onUploadFile(new Action('upload_file', [
            'type' => 'data',
            'name' => 'b.txt',
            'data' => base64_encode('hello world'),
            'sha256' => hash('sha256', 'hello world'),
        ]));
        $this->assertEquals(RetCode::OK, $resp->retcode);
        $this->assertArrayHasKey('file_id', $resp->data);
        $path = ob_config('file_upload.path', getcwd() . '/data/files');
        [$meta, $content] = FileUtil::getMetaFile($path, $resp->data['file_id']);
        $this->assertEquals('b.txt', $meta['name']);
        $this->assertEquals('hello world', $content);
    }
}
