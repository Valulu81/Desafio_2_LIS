# UDB Academy SV — Sistema de Cotización

> Sistema web para navegar servicios, gestionarlos en un carrito y generar cotizaciones con descuentos e IVA automáticos.

**Estudiantes:**
- Valeria Liseth Paredes Lara
- Andre Emanuel Preza Deras

---

## Tabla de Contenidos

- [Descripción](#descripción)
- [Tecnologías](#tecnologías)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Base de Datos](#base-de-datos)
- [Diagrama de Clases](#diagrama-de-clases)
- [Flujo del Sistema](#flujo-del-sistema)
- [Reglas del Sistema](#reglas-del-sistema)
- [Instalación](#instalación)
- [Uso](#uso)

---

## Descripción

UDB Academy SV es una aplicación web que permite a usuarios registrados explorar un catálogo de servicios, agregarlos a un carrito de compras, modificar cantidades y finalmente generar una cotización formal con código único, descuentos por volumen e IVA incluido.

Los administradores tienen acceso a un panel adicional para crear, editar y eliminar servicios del catálogo.

---

## Tecnologías

| Tecnología | Uso |
|---|---|
| PHP 8+ | Backend, sesiones, controladores y modelos |
| MySQL | Base de datos relacional |
| JavaScript (AJAX) | Interacción sin recargar la página |
| Bootstrap 5.3 | Diseño responsivo |
| Bootstrap Icons | Iconografía |
| JSON | Comunicación entre frontend y backend |
| WAMP Server | Entorno de desarrollo local |

---

## Estructura del Proyecto

```
Practico_2/
├── config/
│   ├── database.php          # Conexión PDO 
│   └── database.sql          # Script de base de datos
│
├── controllers/
│   ├── AuthController.php    # Login, register, logout
│   ├── CartController.php    # Vista del carrito
│   ├── QuoteController.php   # Vista de cotizaciones
│   └── ServiceController.php # CRUD de servicios
│
├── models/
│   ├── Quote.php             # Lógica de cotizaciones
│   ├── QuoteDetail.php       # Detalle de servicios por cotización
│   ├── Service.php           # Operaciones sobre servicios
│   └── User.php              # Autenticación y registro
│
├── public/
│   ├── index.php             # Router principal
│   ├── add-to-cart.php       # Endpoint AJAX — agregar al carrito
│   ├── remove-from-cart.php  # Endpoint AJAX — eliminar del carrito
│   ├── update-cart.php       # Endpoint AJAX — actualizar cantidad
│   ├── process-quote.php     # Endpoint AJAX — generar cotización
│   ├── main.js               # JS global (CRUD servicios)
│   ├── cart.js               # JS del carrito
│   └── styles.css            # Estilos personalizados
│
└── views/
    ├── auth.php              # Login y registro
    ├── services.php          # Catálogo de servicios
    ├── admin_services.php    # Panel admin de servicios
    ├── cart.php              # Vista del carrito
    └── quotes.php            # Historial de cotizaciones
```

---

## Base de Datos

### Diagrama Entidad-Relación

```
┌─────────────────┐         ┌──────────────────────┐         ┌─────────────────┐
│     USERS       │         │       QUOTES          │         │    SERVICES     │
├─────────────────┤         ├──────────────────────┤         ├─────────────────┤
│ PK id           │◄────────│ PK id                │         │ PK id           │
│    name         │  1   N  │ FK client_id          │         │    title        │
│    email        │         │    code  (COT-YYYY-##)│         │    category     │
│    company      │         │    total              │         │    price        │
│    telephone    │         │    created_at         │         │    image_url    │
│    password     │         │    valid_until        │         └────────┬────────┘
│    role         │         └──────────┬───────────┘                  │
└─────────────────┘                    │ 1                             │ 1
                                       │                               │
                                       │ N                             │ N
                              ┌────────▼───────────┐                  │
                              │   QUOTE_SERVICES   │◄─────────────────┘
                              ├────────────────────┤
                              │ PK id              │
                              │ FK quote_id        │
                              │ FK service_id      │
                              │    quantity        │
                              │    unit_price      │
                              └────────────────────┘
```

### Relaciones

| Relación | Tipo | Descripción |
|---|---|---|
| users → quotes | 1 a N | Un usuario puede tener múltiples cotizaciones |
| quotes → quote_services | 1 a N | Una cotización tiene múltiples servicios |
| services → quote_services | 1 a N | Un servicio puede estar en múltiples cotizaciones |

---

## 🏗️ Diagrama de Clases

```
┌──────────────────────────────────┐
│           AuthController         │
├──────────────────────────────────┤
│ - userModel: User                │
├──────────────────────────────────┤
│ + login(): void                  │
│ + register(): void               │
│ + logout(): void                 │
└──────────────┬───────────────────┘
               │ usa
               ▼
┌──────────────────────────────────┐
│              User                │
├──────────────────────────────────┤
│ - db: PDO                        │
├──────────────────────────────────┤
│ + login(email, password): array  │
│ + register(data): array          │
│ + getById(id): array             │
│ - emailExists(email): bool       │
└──────────────────────────────────┘


┌──────────────────────────────────┐
│         ServiceController        │
├──────────────────────────────────┤
│ - serviceModel: Service          │
├──────────────────────────────────┤
│ + index(): void                  │
│ + adminIndex(): void             │
│ + ajaxCreate(): void             │
│ + ajaxUpdate(): void             │
│ + ajaxDelete(): void             │
│ + ajaxGetById(): void            │
└──────────────┬───────────────────┘
               │ usa
               ▼
┌──────────────────────────────────┐
│             Service              │
├──────────────────────────────────┤
│ - db: PDO                        │
│ + CATEGORIAS_VALIDAS: array      │
│ + PRECIO_MIN: int                │
│ + PRECIO_MAX: int                │
├──────────────────────────────────┤
│ + getAll(): array                │
│ + getById(id): array             │
│ + create(data): array            │
│ + update(id, data): array        │
│ + delete(id): array              │
│ + validarDatos(data): array      │
└──────────────────────────────────┘


┌──────────────────────────────────┐
│          QuoteController         │
├──────────────────────────────────┤
│ - quoteModel: Quote              │
├──────────────────────────────────┤
│ + index(): void                  │
│ + procesarCotizacion(): void     │
└──────────────┬───────────────────┘
               │ usa
               ▼
┌──────────────────────────────────┐       ┌──────────────────────────────┐
│             Quote                │       │         QuoteDetail           │
├──────────────────────────────────┤       ├──────────────────────────────┤
│ - db: PDO                        │       │ - db: PDO                    │
│ - codigo: string                 │       ├──────────────────────────────┤
│ - items: array                   │       │ + save(quoteId, serviceId,   │
│ - subtotal: float                │       │        qty, price): int      │
│ - descuento: float               │       │ + getByQuoteId(id): array    │
│ - iva: float                     │       └──────────────────────────────┘
│ - total: float                   │
├──────────────────────────────────┤
│ + generar(cliente, cart): array  │
│ + getByUserId(id): array         │
│ + getAll(): array                │
│ + getDetalle(id): array          │
│ + calcularSubtotal(): float      │
│ + calcularDescuento(): float     │
│ + calcularIVA(): float           │
│ + calcularTotal(): float         │
│ + generarCodigo(): string        │
│ + validarMonto(sub): bool        │
└──────────────────────────────────┘
```

---

## Flujo del Sistema

```
Usuario abre el sistema
         │
         ▼
  ¿Está logueado?
    /           \
  NO            SÍ
   │             │
   ▼             ▼
Login/      Catálogo de
Registro     Servicios
                │
                ▼
         Agregar al carrito
         (AJAX → add-to-cart.php)
                │
                ▼
         $_SESSION['cart']
         se actualiza
                │
                ▼
         ¿Modifica cantidades?
          /           \
        SÍ            NO
         │             │
         ▼             │
    update-cart.php    │
         │             │
         └──────┬──────┘
                ▼
         ¿Presiona Cotizar?
          /           \
        NO            SÍ
         │             │
         ▼             ▼
    Sigue        Formulario
    editando     del cliente
                      │
                      ▼
               process-quote.php
               → Quote::generar()
                      │
                      ▼
               Guarda en BD:
               quotes + quote_services
                      │
                      ▼
               Vacía el carrito
                      │
                      ▼
               Modal de confirmación
               con código COT-YYYY-####
```

---

## Reglas del Sistema

### Descuentos por cantidad de items

| Monto | Descuento |
|---|---|
| 500 — 999 | 5% |
| 1000 — 2499 | 10% |
| 2500+ | 15% |



### Código de cotización

Formato: `COT-YYYY-####`

Ejemplo: `COT-2026-0001`

- `YYYY` = año actual
- `####` = consecutivo con 4 dígitos, reinicia cada año

### Vigencia de cotización

Las cotizaciones son válidas por **7 días** desde su creación. La fecha se calcula automáticamente en la base de datos con `DEFAULT (CURRENT_TIMESTAMP + INTERVAL 7 DAY)`.

---

## Instalación

### Requisitos

- WAMP Server (o XAMPP)
- PHP 8.0 o superior
- MySQL 5.7 o superior
- Navegador moderno

### Credenciales de prueba

| Rol | Email | Contraseña |
|---|---|---|
| Admin | admin@academy_sv.com | password1234 |

---

## 👥 Autores

| Valeria Liseth Paredes Lara | 
| Andre Emanuel Preza Deras | 

---

*UDB Academy SV — Proyecto Práctico 2*
