<?php

namespace Imhonet\Connection\Query\PDO;


use Imhonet\Connection\Query\Query;

abstract class PDO extends Query
{
    private $statements = array();
    private $params = array();
    private $placeholders = array();

    /**
     * @var \PDOStatement|null
     */
    private $response;
    /**
     * @var bool|null
     */
    private $success;

    /**
     * @param string $statement
     * @return $this
     */
    public function addStatement($statement)
    {
        $this->statements[] = $statement;

        return $this;
    }

    /**
     * @param array|float|int|null|string ...$param [optional]
     * @return $this
     */
    public function addParams()
    {
        $params = array();

        foreach (func_get_args() as $param) {
            if (is_array($param)) {
                $params = array_merge($params, $param);
                $this->addPlaceholder(sizeof($param));
            } else {
                $params[] = $param;
                $this->addPlaceholder(1);
            }
        }

        $this->params[ $this->getStatementId() ] = $params;

        return $this;
    }

    /**
     * @param int $count
     * @return $this
     */
    private function addPlaceholder($count)
    {
        $placeholders = & $this->placeholders[ $this->getStatementId() ];
        $placeholders[] = $count > 0 ? str_repeat('?,', $count - 1) . '?' : null;

        return $this;
    }

    private function getStatementId()
    {
        return $this->statements ? count($this->statements) - 1 : 0;
    }

    /**
     * @return \PDOStatement|bool
     */
    public function execute()
    {
        return $this->getResponse();
    }

    protected function getResponse()
    {
        if (!$this->hasResponse()) {
            try {
                $stmt = $this->getStmt($this->getStatement(), $this->getParams());
            } catch (\Exception $e) {
                $this->success = false;
                $this->response = false;
            }

            if (isset($stmt)) {
                $this->success = $stmt->execute();
                $this->response = $stmt;
            }
        }

        return $this->response;
    }

    protected function getStmt($statement, array $params = array())
    {
        try {
            $stmt = $this->getResource()->prepare($statement);
        } catch (\PDOException $e) {
            throw $e;
        }

        foreach ($params as $i => $param) {
            $stmt->bindValue($i + 1, $param, is_numeric($param) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        return $stmt;
    }

    protected function getStatement()
    {
        $statement_id = key($this->statements);

        return vsprintf($this->statements[$statement_id], $this->placeholders[$statement_id]);
    }

    protected function getParams()
    {
        $statement_id = key($this->statements);

        return isset($this->params[$statement_id]) ? $this->params[$statement_id] : array();
    }

    protected function hasResponse()
    {
        return $this->success !== null;
    }

    /**
     * @inheritdoc
     * @return \PDO
     */
    protected function getResource()
    {
        return parent::getResource();
    }

    private function isError()
    {
        return $this->getResponse() === null || $this->success === false;
    }

    /**
     * @inheritdoc
     */
    public function getErrorCode()
    {
        return (int) $this->isError();
    }

}
