<?php
// models/QuoteDetail.php
// Esta clase representa el detalle de una cotización,
// es decir cada servicio individual que forma parte de ella.
// Maneja la tabla quote_services de la BD.

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

    // ── GETTERS ───────────────────────────────────────────────────────────
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

    // ── GUARDAR DETALLE ───────────────────────────────────────────────────
    // Inserta un servicio como parte de una cotización en quote_services.
    // Lo usa Quote.php internamente cuando genera una cotización.

    public function save($quoteId, $serviceId, $quantity, $unitPrice)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO quote_services (quote_id, service_id, quantity, unit_price)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$quoteId, $serviceId, $quantity, $unitPrice]);
        return $this->db->lastInsertId();
    }

    // ── OBTENER DETALLES POR COTIZACIÓN ───────────────────────────────────
    // Devuelve todos los servicios de una cotización específica.
    // Hace JOIN con services para traer el nombre y categoría del servicio.

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
