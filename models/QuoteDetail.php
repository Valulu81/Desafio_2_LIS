<?php
require_once __DIR__ . '/../config/Database.php';

class QuoteDetail
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function insertService(int $quoteId, int $serviceId, float $unit_price, int $quantity): bool
    {
        $sql  = "INSERT INTO quote_services (quote_id, service_id, quantity, unit_price) VALUES (:quote_id, :service_id, :quantity, :unit_price)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'quote_id'   => $quoteId,
            'service_id' => $serviceId,
            'quantity'   => $quantity,
            'unit_price' => $unit_price
        ]);
    }

    public function getServicesByQuoteId(int $quoteId): array
    {
        $sql = "SELECT qs.quantity, qs.unit_price, s.title,
                       (qs.quantity * qs.unit_price) AS subtotal
                FROM quote_services qs
                JOIN services s ON qs.service_id = s.id
                WHERE qs.quote_id = :quote_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['quote_id' => $quoteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}