<?php
require_once __DIR__ . '/../config/database.php';

class QuoteDetail
{

    // Propiedades privadas — una por cada columna de quote_services
    private $id;
    private $quoteId;
    private $serviceId;
    private $quantity;
    private $unitPrice;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId()
    {
        return $this->id;
    }
    public function getQuoteId()
    {
        return $this->quoteId;
    }
    public function getServiceId()
    {
        return $this->serviceId;
    }
    public function getQuantity()
    {
        return $this->quantity;
    }
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    // inserta un nuevo detalle de cotización en la tabla quote_services
    public function save($quoteId, $serviceId, $quantity, $unitPrice)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO quote_services (quote_id, service_id, quantity, unit_price)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$quoteId, $serviceId, $quantity, $unitPrice]);
        return $this->db->lastInsertId();
    }

    // obtiene los detalles de una cotización específica, incluyendo el título y categoría del servicio
    public function getByQuoteId($quoteId)
    {
        $stmt = $this->db->prepare(
            "SELECT qs.*, s.title, s.category
             FROM quote_services qs
             JOIN services s ON qs.service_id = s.id
             WHERE qs.quote_id = ?"
        );
        $stmt->execute([$quoteId]);
        return $stmt->fetchAll();
    }
}
