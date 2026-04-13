<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../models/Quote.php';
require_once __DIR__ . '/../models/QuoteDetail.php';

class QuoteController
{
    private $quoteModel;
    private $detailModel;

    public function __construct()
    {
        $this->quoteModel  = new Quote();
        $this->detailModel = new QuoteDetail();
    }

    // cotizaciones del usuario logueado
    public function index(): void
    {
        $this->requireLogin();
        $quotes = $this->quoteModel->getQuotesByClientId($_SESSION['user_id']);
        require_once __DIR__ . '/../views/quotes.php';
    }

    // todas las cotizaciones — solo admin
    public function adminIndex(): void
    {
        $this->requireAdmin();
        $quotes = $this->quoteModel->getAllQuotes();
        require_once __DIR__ . '/../views/admin_quotes.php';
    }

    // detalle de una cotización por código de cotizacion no de id 
    public function ajaxGetByCode(): void
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $code = trim($_GET['code'] ?? '');

        if (empty($code)) {
            $this->jsonError('Código inválido');
        }

        $quote = $this->quoteModel->getQuoteByCode($code);

        if (!$quote) {
            $this->jsonError('Cotización no encontrada');
        }

        // verifica que el usuario solo vea sus propias cotizaciones
        // a menos que sea admin
        if ($_SESSION['user_role'] !== 'admin' && $quote['client_id'] !== $_SESSION['user_id']) {
            $this->jsonError('Acceso denegado');
        }

        $servicios = $this->detailModel->getServicesByQuoteId($quote['id']);

        echo json_encode([
            'success'  => true,
            'quote'    => $quote,
            'servicios' => $servicios
        ]);
        exit;
    }

    // helpers
    private function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    private function requireAdmin(): void
    {
        $this->requireLogin();
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?action=services');
            exit;
        }
    }

    private function jsonError(string $msg): void
    {
        echo json_encode(['success' => false, 'message' => $msg]);
        exit;
    }
}