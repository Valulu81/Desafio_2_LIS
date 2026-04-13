<?php

require_once __DIR__ . '/../config/database.php';

class Quote
{

    // Propiedades privadas requeridas por el proyecto
    private $codigo;
    private $cliente;
    private $items;
    private $subtotal;
    private $descuento;
    private $iva;
    private $total;
    private $db;

    public function __construct()
    {
        $this->db    = Database::getInstance()->getConnection();
        $this->items = []; // El carrito empieza vacío
    }

    public function getCodigo()
    {
        return $this->codigo;
    }
    public function getCliente()
    {
        return $this->cliente;
    }
    public function getItems()
    {
        return $this->items;
    }
    public function getSubtotal()
    {
        return $this->subtotal;
    }
    public function getDescuento()
    {
        return $this->descuento;
    }
    public function getIva()
    {
        return $this->iva;
    }
    public function getTotal()
    {
        return $this->total;
    }

    // sgrega item a cotizacion
    public function agregarItem($item)
    {
        $this->items[] = $item;
    }

    // calcula el subtotal sumando precio * cantidad de cada item
    public function calcularSubtotal()
    {
        $this->subtotal = 0;
        foreach ($this->items as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }
        return $this->subtotal;
    }

    // descuento
    public function calcularDescuento()
    {
        if ($this->subtotal >= 2500) {
            $this->descuento = $this->subtotal * 0.15;
        } elseif ($this->subtotal >= 1000) {
            $this->descuento = $this->subtotal * 0.10;
        } elseif ($this->subtotal >= 500) {
            $this->descuento = $this->subtotal * 0.05;
        } else {
            $this->descuento = 0;
        }
        return $this->descuento;
    }

    //iva
    public function calcularIVA()
    {
        $this->iva = ($this->subtotal - $this->descuento) * 0.13;
        return $this->iva;
    }

    // total
    public function calcularTotal()
    {
        $this->total = $this->subtotal - $this->descuento + $this->iva;
        return $this->total;
    }

    // codigo
    public static function generarCodigo()
    {
        $db   = Database::getInstance()->getConnection();
        $anio = date('Y'); // Año actual, ej: 2026

        // Contamos cuántas cotizaciones hay este año para el consecutivo
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM quotes WHERE YEAR(created_at) = ?"
        );
        $stmt->execute([$anio]);
        $count = $stmt->fetchColumn();

        // El consecutivo empieza en 1 y se formatea con 4 dígitos: 0001, 0002...
        $consecutivo = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return 'COT-' . $anio . '-' . $consecutivo;
    }

        // minimo monto
    public static function validarMonto($subtotal)
    {
        return $subtotal >= 100;
    }

    // genera cotizacion
    public function generar($clienteData, $cart)
    {
        // Validamos que el carrito no esté vacío
        if (empty($cart)) {
            return ['success' => false, 'message' => 'El carrito está vacío'];
        }

        // Cargamos los items del carrito en la propiedad $items
        foreach ($cart as $item) {
            $this->agregarItem($item);
        }

        // Hacemos todos los cálculos en orden
        $this->calcularSubtotal();
        $this->calcularDescuento();
        $this->calcularIVA();
        $this->calcularTotal();

        // Validamos el monto mínimo de $100
        if (!self::validarMonto($this->subtotal)) {
            return ['success' => false, 'message' => 'El subtotal mínimo es $100'];
        }

        // Generamos el código único de la cotización
        $this->codigo  = self::generarCodigo();
        $this->cliente = $clienteData;

        try {
            $this->db->beginTransaction();

            // Insertamos la cotización principal en la tabla quotes
            $stmt = $this->db->prepare(
                "INSERT INTO quotes (code, client_id, total)
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([
                $this->codigo,
                $clienteData['id'],
                round($this->total, 2)
            ]);

            // Obtenemos el ID de la cotización recién creada
            $quoteId = $this->db->lastInsertId();

            // Insertamos cada servicio en quote_services
            $stmtDetalle = $this->db->prepare(
                "INSERT INTO quote_services (quote_id, service_id, quantity, unit_price)
                 VALUES (?, ?, ?, ?)"
            );

            foreach ($this->items as $item) {
                $stmtDetalle->execute([
                    $quoteId,
                    $item['id'],
                    $item['quantity'],
                    $item['price']
                ]);
            }

            // Si todo salió bien confirmamos la transacción
            $this->db->commit();

            return [
                'success'   => true,
                'codigo'    => $this->codigo,
                'subtotal'  => number_format($this->subtotal,  2),
                'descuento' => number_format($this->descuento, 2),
                'iva'       => number_format($this->iva,       2),
                'total'     => number_format($this->total,     2)
            ];
        } catch (PDOException $e) {
            // Si algo falló revertimos todo para no dejar datos a medias
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al guardar la cotización'];
        }
    }

    // cotizacion por usuario (para clientes)
    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare(
            "SELECT q.*, 
                    COUNT(qs.id) as cantidad_servicios
             FROM quotes q
             LEFT JOIN quote_services qs ON q.id = qs.quote_id
             WHERE q.client_id = ?
             GROUP BY q.id
             ORDER BY q.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // pal admin
    public function getAll()
    {
        $stmt = $this->db->prepare(
            "SELECT q.*,
                    u.name as cliente_nombre,
                    COUNT(qs.id) as cantidad_servicios
             FROM quotes q
             LEFT JOIN users u ON q.client_id = u.id
             LEFT JOIN quote_services qs ON q.id = qs.quote_id
             GROUP BY q.id
             ORDER BY q.created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // servicios
    public function getDetalle($quoteId)
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
