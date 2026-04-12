<?php
require_once __DIR__ . '/../config/Database.php';

class Quote
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createQuote(array $data): bool
    {
        $sql  = "INSERT INTO quotes (code, client_id, total) VALUES (:code, :client_id, :total)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getQuotesByClientId(int $clientId): array
    {
        $sql = "SELECT q.id, q.code, q.total, q.created_at, q.valid_until,
                    u.name AS client_name, u.email, u.telephone, u.company,
                    (SELECT COUNT(*) FROM quote_services WHERE quote_id = q.id) AS service_count
                FROM quotes q
                JOIN users u ON q.client_id = u.id
                WHERE q.client_id = :client_id
                ORDER BY q.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllQuotes(): array
    {
        $sql = "SELECT q.id, q.code, q.total, q.created_at, q.valid_until,
                    u.name AS client_name, u.email, u.telephone, u.company,
                    (SELECT COUNT(*) FROM quote_services WHERE quote_id = q.id) AS service_count
                FROM quotes q
                JOIN users u ON q.client_id = u.id
                ORDER BY q.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuoteByCode(string $code): ?array
    {
        $sql = "SELECT q.id, q.code, q.total, q.created_at, q.valid_until,
                    u.name AS client_name, u.email, u.telephone, u.company,
                    (SELECT COUNT(*) FROM quote_services WHERE quote_id = q.id) AS service_count
                FROM quotes q
                JOIN users u ON q.client_id = u.id
                WHERE q.code = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['code' => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function generateUniqueCode(): string
    {
        $year = date('Y');
        $sql  = "SELECT COUNT(*) FROM quotes WHERE YEAR(created_at) = :year";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['year' => $year]);
        $count = $stmt->fetchColumn() + 1;
        return sprintf('COT-%s-%04d', $year, $count);
    }
}
