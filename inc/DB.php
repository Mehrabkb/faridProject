<?php

class DB
{
    private PDO $pdo;

    private string $table = '';
    private string $select = '*';

    private array $where = [];
    private array $bindings = [];

    private array $joins = [];
    private array $order = [];

    private ?int $limit = null;

    public function __construct()
    {
        $config = require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'CONFIG.php';
        $config = $config['db'];
        $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset=utf8";

        $this->pdo = new PDO(
            $dsn,
            $config['user'],
            $config['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    private function reset()
    {
        $this->table = '';
        $this->select = '*';
        $this->where = [];
        $this->bindings = [];
        $this->joins = [];
        $this->order = [];
        $this->limit = null;
    }

    /* ================= SELECT ================= */

    public function select($fields='*')
    {
        $this->select = $fields;
        return $this;
    }

    public function from($table)
    {
        $this->table = $table;
        return $this;
    }

    /* ================= WHERE ================= */

    public function where($column,$operator,$value)
    {
        $param = ':w'.count($this->bindings);

        $this->where[] = "$column $operator $param";
        $this->bindings[$param] = $value;

        return $this;
    }

    public function orWhere($column,$operator,$value)
    {
        $param = ':w'.count($this->bindings);

        if(count($this->where))
            $this->where[] = "OR $column $operator $param";
        else
            $this->where[] = "$column $operator $param";

        $this->bindings[$param] = $value;

        return $this;
    }

    /* ================= JOINS ================= */

    public function innerJoin($table,$first,$operator,$second)
    {
        $this->joins[] = "INNER JOIN $table ON $first $operator $second";
        return $this;
    }

    public function leftJoin($table,$first,$operator,$second)
    {
        $this->joins[] = "LEFT JOIN $table ON $first $operator $second";
        return $this;
    }

    public function rightJoin($table,$first,$operator,$second)
    {
        $this->joins[] = "RIGHT JOIN $table ON $first $operator $second";
        return $this;
    }

    /* ================= ORDER / LIMIT ================= */

    public function orderBy($column,$dir='ASC')
    {
        $this->order[] = "$column ".strtoupper($dir);
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /* ================= GET ================= */

    public function get()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if($this->joins)
            $sql .= " ".implode(' ',$this->joins);

        if($this->where)
            $sql .= " WHERE ".implode(' ',$this->where);

        if($this->order)
            $sql .= " ORDER BY ".implode(',',$this->order);

        if($this->limit)
            $sql .= " LIMIT {$this->limit}";

        $stmt = $this->pdo->prepare($sql);

        foreach($this->bindings as $k=>$v)
            $stmt->bindValue($k,$v);

        $stmt->execute();

        $data = $stmt->fetchAll();

        $this->reset();

        return $data;
    }

    public function first()
    {
        $this->limit(1);
        $data = $this->get();
        return $data[0] ?? null;
    }

    /* ================= INSERT ================= */

    public function insert($table,$data)
    {
        $columns = implode(',',array_keys($data));
        $params = ':'.implode(',:',array_keys($data));

        $sql = "INSERT INTO $table ($columns) VALUES ($params)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($data);
    }

    /* ================= UPDATE ================= */

    public function update($table,$data)
    {
        $set = [];

        foreach($data as $k=>$v)
            $set[]="$k=:$k";

        $sql = "UPDATE $table SET ".implode(',',$set);

        if($this->where)
            $sql.=" WHERE ".implode(' ',$this->where);

        $stmt = $this->pdo->prepare($sql);

        foreach($data as $k=>$v)
            $stmt->bindValue(":$k",$v);

        foreach($this->bindings as $k=>$v)
            $stmt->bindValue($k,$v);

        $res=$stmt->execute();

        $this->reset();

        return $res;
    }

    /* ================= DELETE ================= */

    public function delete($table)
    {
        $sql="DELETE FROM $table";

        if($this->where)
            $sql.=" WHERE ".implode(' ',$this->where);

        $stmt=$this->pdo->prepare($sql);

        foreach($this->bindings as $k=>$v)
            $stmt->bindValue($k,$v);

        $res=$stmt->execute();

        $this->reset();

        return $res;
    }

    /* ================= TRANSACTION ================= */

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollback()
    {
        if($this->pdo->inTransaction())
            return $this->pdo->rollBack();
    }

    /* ================= RAW QUERY ================= */

    public function query($sql,$params=[])
    {
        $stmt=$this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
