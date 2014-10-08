<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Application\Auth;

use Application\Exception;
use Application\Users;
use Bluz\Application;
use Bluz\Auth\AbstractTable;
use Bluz\Auth\AuthException;
use Bluz\Proxy\Auth;

/**
 * Auth Table
 *
 * @package  Application\Auth
 *
 * @method   static Row findRow($primaryKey)
 * @method   static Row findRowWhere($whereList)
 *
 * @author   Anton Shevchuk
 * @created  12.07.11 15:28
 */
class Table extends AbstractTable
{
    /**
     * Time that the token remains valid
     */
    const TOKEN_EXPIRATION_TIME = 1800;

    /**
     * authenticate user by login/pass
     *
     * @param string $username
     * @param string $password
     * @throws AuthException
     * @return void
     */
    public function authenticateEquals($username, $password)
    {
        $authRow = $this->checkEquals($username, $password);

        // get user profile
        $user = Users\Table::findRow($authRow->userId);

        // try to login
        $user->login();
    }

    /**
     * check user by login/pass
     *
     * @param string $username
     * @param string $password
     * @throws AuthException
     * @return Row
     */
    public function checkEquals($username, $password)
    {
        $authRow = $this->getAuthRow(self::PROVIDER_EQUALS, $username);

        if (!$authRow) {
            throw new AuthException("User not found");
        }

        // encrypt password
        $encrypt = $this->callEncryptFunction($password, $authRow->tokenSecret);

        if ($encrypt != $authRow->token) {
            throw new AuthException("Wrong password");
        }

        // get auth row
        return $authRow;
    }

    /**
     * authenticate user by login/pass
     *
     * @param Users\Row $user
     * @param string $password
     * @throws AuthException
     * @return Row
     */
    public function generateEquals($user, $password)
    {
        // clear previous generated Auth record
        // works with change password
        $this->delete(
            [
                'userId' => $user->id,
                'foreignKey' => $user->login,
                'provider' => self::PROVIDER_EQUALS,
                'tokenType' => self::TYPE_ACCESS
            ]
        );

        // new auth row
        $row = new Row();
        $row->userId = $user->id;
        $row->foreignKey = $user->login;
        $row->provider = self::PROVIDER_EQUALS;
        $row->tokenType = self::TYPE_ACCESS;

        // generate secret
        $row->tokenSecret = $this->generateSecret($user->id);

        // encrypt password and save as token
        $row->token = $this->callEncryptFunction($password, $row->tokenSecret);

        $row->save();

        return $row;
    }

    /**
     * callEncryptFunction
     *
     * @param string $password
     * @param string $secret
     * @throws \Application\Exception
     * @return string
     */
    protected function callEncryptFunction($password, $secret)
    {
        /** @var \Bluz\Auth\Auth $auth */
        $auth = Auth::getInstance();
        $options = $auth->getOption(self::PROVIDER_EQUALS);

        if (!isset($options['encryptFunction']) or
            !is_callable($options['encryptFunction'])
        ) {
            throw new Exception("Encryption function for 'equals' adapter is not callable");
        }

        // encrypt password with secret
        return call_user_func($options['encryptFunction'], $password, $secret);
    }

    /**
     * authenticate user by token
     *
     * @param string $token
     * @throws \Bluz\Auth\AuthException
     * @return void
     */
    public function authenticateToken($token)
    {
        $authRow = $this->checkToken($token);

        // get user profile
        $user = Users\Table::findRow($authRow->userId);

        // try to login
        $user->login();
    }

    /**
     * authenticate user by token
     *
     * @param string $token
     * @throws \Bluz\Auth\AuthException
     * @return Row
     */
    public function checkToken($token)
    {
        if (!$authRow = $this->findRowWhere(['token' =>  $token])) {
            throw new AuthException('Invalid token');
        }

        if (strtotime($authRow->created) + self::TOKEN_EXPIRATION_TIME < time()) {
            throw new AuthException('Token has expired');
        }

        return $authRow;
    }

    /**
     * @param $equalAuth
     * @return Row
     * @throws Exception
     * @throws \Bluz\Db\Exception\DbException
     */
    public function generateToken($equalAuth)
    {
        // clear previous generated Auth record
        // works with change password
        $this->delete(
            [
                'userId' => $equalAuth->userId,
                'foreignKey' => $equalAuth->foreignKey,
                'provider' => self::PROVIDER_TOKEN,
                'tokenType' => self::TYPE_ACCESS
            ]
        );

        // new auth row
        $row = new Row();
        $row->userId = $equalAuth->userId;
        $row->foreignKey = $equalAuth->foreignKey;
        $row->provider = self::PROVIDER_TOKEN;
        $row->tokenType = self::TYPE_ACCESS;

        // generate secret
        $row->tokenSecret = $this->generateSecret($equalAuth->userId);

        // encrypt password and save as token
        $row->token = $this->callEncryptFunction($equalAuth->token, $row->tokenSecret);

        $row->save();

        return $row;
    }
}
