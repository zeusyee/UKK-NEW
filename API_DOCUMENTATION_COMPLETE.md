# 📚 UKK Project Management API - Dokumentasi Lengkap

> Dokumentasi API lengkap dengan semua informasi yang Anda butuhkan untuk testing di Postman dan integrasi ke aplikasi

**Version:** 1.0.0  
**Status:** ✅ Production Ready  
**Total Endpoints:** 29  
**Last Updated:** 7 November 2025

---

## 📖 Daftar Isi

1. [Overview](#overview)
2. [Setup & Instalasi](#setup--instalasi)
3. [Authentication](#authentication)
4. [API Endpoints](#api-endpoints)
5. [Postman Setup](#postman-setup)
6. [Contoh Workflow](#contoh-workflow)
7. [Troubleshooting](#troubleshooting)

---

## Overview

### Informasi Umum

- **Base URL:** `http://localhost:8000/api/v1`
- **Authentication:** Bearer Token (Laravel Sanctum)
- **Response Format:** JSON
- **Content-Type:** `application/json`

### Fitur API

✅ User Authentication (Login, Register, Logout)  
✅ Project Management  
✅ Board & Card Management  
✅ Subtask Management dengan Status Tracking  
✅ Time Logging & Tracking  
✅ Member Dashboard & Statistics  
✅ Comprehensive Error Handling  

### Total 29 Endpoints

| Category | Count | 
|----------|-------|
| Authentication | 5 |
| Projects | 3 |
| Cards | 6 |
| Subtasks | 8 |
| Time Logs | 2 |
| Member Dashboard | 2 |

---

## Setup & Instalasi

### Prerequisites

```
✅ Postman (https://www.postman.com/downloads/)
✅ Laravel server running: http://localhost:8000
✅ Database sudah di-setup
✅ Ada data user di database
```

### Quick Setup (3 Langkah)

**Step 1: Siapkan Database**
```bash
# Pastikan server sudah running
php artisan serve
```

**Step 2: Test koneksi ke API**
```bash
curl http://localhost:8000/api/v1/auth/login
```

**Step 3: Import ke Postman**
Instruksi ada di bagian [Postman Setup](#postman-setup)

---

## Authentication

### Bearer Token

Semua endpoint (kecuali login & register) memerlukan **Bearer Token** di header:

```
Authorization: Bearer {token}
```

### Endpoint Autentikasi

#### 1. Login
**Endpoint:** `POST /auth/login`

**Deskripsi:** Login dengan email dan password

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Success Response (200):**
```json
{
  "message": "Login berhasil",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": "user_123",
    "name": "John Doe",
    "email": "user@example.com",
    "role": "member",
    "created_at": "2025-11-07T10:00:00Z"
  }
}
```

**Error Response (401):**
```json
{
  "message": "Email atau password salah"
}
```

---

#### 2. Register
**Endpoint:** `POST /auth/register`

**Deskripsi:** Registrasi user baru

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Success Response (201):**
```json
{
  "message": "User berhasil didaftarkan",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": "user_123",
    "name": "John Doe",
    "email": "john@example.com",
    "role": "member",
    "created_at": "2025-11-07T10:00:00Z"
  }
}
```

---

#### 3. Logout
**Endpoint:** `POST /auth/logout`

**Deskripsi:** Logout dan hapus token

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "message": "Logout berhasil"
}
```

---

#### 4. Get Profile
**Endpoint:** `GET /auth/me`

**Deskripsi:** Mendapatkan profile user yang sedang login

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "user": {
    "id": "user_123",
    "name": "John Doe",
    "email": "user@example.com",
    "role": "member",
    "created_at": "2025-11-07T10:00:00Z"
  }
}
```

---

#### 5. Update Profile
**Endpoint:** `PUT /auth/profile`

**Deskripsi:** Update profile user

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "John Doe Updated",
  "email": "newemail@example.com"
}
```

**Success Response (200):**
```json
{
  "message": "Profil berhasil diupdate",
  "user": {
    "id": "user_123",
    "name": "John Doe Updated",
    "email": "newemail@example.com",
    "role": "member",
    "created_at": "2025-11-07T10:00:00Z"
  }
}
```

---

## API Endpoints

### Projects

#### 1. Get All Projects
**Endpoint:** `GET /projects`

**Deskripsi:** Mendapatkan semua project yang diikuti member

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (integer, optional) - Halaman (default: 1)
- `per_page` (integer, optional) - Jumlah per halaman (default: 10)

**Example URL:** `GET /projects?page=1&per_page=10`

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "proj_001",
      "name": "Website Redesign",
      "description": "Redesign website kami",
      "status": "active",
      "created_at": "2025-10-01T10:00:00Z",
      "creator": {
        "id": "user_456",
        "name": "Admin User"
      },
      "members_count": 5,
      "boards_count": 3
    }
  ],
  "pagination": {
    "total": 1,
    "per_page": 10,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

#### 2. Get Project Detail
**Endpoint:** `GET /projects/{project_id}`

**Deskripsi:** Mendapatkan detail project

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `project_id` (string, required) - ID project

**Example URL:** `GET /projects/proj_001`

**Success Response (200):**
```json
{
  "project": {
    "id": "proj_001",
    "name": "Website Redesign",
    "description": "Redesign website kami",
    "status": "active",
    "created_at": "2025-10-01T10:00:00Z",
    "creator": {
      "id": "user_456",
      "name": "Admin User"
    },
    "members_count": 5,
    "boards_count": 3
  }
}
```

---

#### 3. Get Project Boards
**Endpoint:** `GET /projects/{project_id}/boards`

**Deskripsi:** Mendapatkan semua board dalam project

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `project_id` (string, required) - ID project

**Success Response (200):**
```json
{
  "boards": [
    {
      "id": "board_001",
      "name": "To Do",
      "cards_count": 5,
      "cards": [
        {
          "id": "card_001",
          "title": "Design Homepage",
          "description": "Membuat desain homepage baru",
          "assigned_user_id": "user_123",
          "priority": "high",
          "status": "in_progress"
        }
      ]
    }
  ]
}
```

---

### Cards

#### 1. Get All Cards
**Endpoint:** `GET /cards`

**Deskripsi:** Mendapatkan semua card yang ditugaskan ke member

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` (string, optional) - Filter status: `assigned`, `in_progress`, `completed`
- `page` (integer, optional) - Halaman (default: 1)
- `per_page` (integer, optional) - Jumlah per halaman (default: 10)

**Example URL:** `GET /cards?status=in_progress&page=1&per_page=10`

**Success Response (200):**
```json
{
  "data": [
    {
      "id": "card_001",
      "title": "Design Homepage",
      "description": "Membuat desain homepage baru",
      "priority": "high",
      "status": "in_progress",
      "board": {
        "id": "board_001",
        "name": "To Do"
      },
      "assigned_user": {
        "id": "user_123",
        "name": "John Doe"
      },
      "subtasks_count": 3,
      "completed_subtasks": 1,
      "active_assignment": {
        "id": "assign_001",
        "status": "active",
        "assigned_at": "2025-11-07T10:00:00Z",
        "started_at": "2025-11-07T10:05:00Z",
        "completed_at": null,
        "duration_minutes": 15,
        "duration_hours": 0.25,
        "human_duration": "15 minutes",
        "is_overdue": false
      },
      "created_at": "2025-11-01T10:00:00Z",
      "updated_at": "2025-11-07T10:30:00Z"
    }
  ],
  "pagination": {
    "total": 5,
    "per_page": 10,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

#### 2. Get My Tasks
**Endpoint:** `GET /cards/my-tasks`

**Deskripsi:** Mendapatkan task yang ditugaskan ke member (alias dari /cards)

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response:** Sama dengan Get All Cards

---

#### 3. Get Card Detail
**Endpoint:** `GET /cards/{card_id}`

**Deskripsi:** Mendapatkan detail card

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `card_id` (string, required) - ID card

**Success Response (200):**
```json
{
  "card": {
    "id": "card_001",
    "title": "Design Homepage",
    "description": "Membuat desain homepage baru",
    "priority": "high",
    "status": "in_progress",
    "board": {
      "id": "board_001",
      "name": "To Do"
    },
    "assigned_user": {
      "id": "user_123",
      "name": "John Doe"
    },
    "subtasks_count": 3,
    "completed_subtasks": 1,
    "active_assignment": {
      "id": "assign_001",
      "status": "active",
      "assigned_at": "2025-11-07T10:00:00Z",
      "started_at": "2025-11-07T10:05:00Z",
      "completed_at": null,
      "duration_minutes": 15,
      "duration_hours": 0.25,
      "human_duration": "15 minutes",
      "is_overdue": false
    },
    "created_at": "2025-11-01T10:00:00Z",
    "updated_at": "2025-11-07T10:30:00Z"
  }
}
```

---

#### 4. Get Cards by Board
**Endpoint:** `GET /boards/{board_id}/cards`

**Deskripsi:** Mendapatkan card berdasarkan board

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `board_id` (string, required) - ID board

**Success Response (200):**
```json
{
  "cards": [
    {
      "id": "card_001",
      "title": "Design Homepage",
      "description": "Membuat desain homepage baru",
      "priority": "high",
      "status": "in_progress",
      "board": {
        "id": "board_001",
        "name": "To Do"
      },
      "assigned_user": {
        "id": "user_123",
        "name": "John Doe"
      },
      "subtasks_count": 3,
      "completed_subtasks": 1,
      "active_assignment": null,
      "created_at": "2025-11-01T10:00:00Z",
      "updated_at": "2025-11-07T10:30:00Z"
    }
  ]
}
```

---

#### 5. Start Card
**Endpoint:** `POST /cards/{card_id}/start`

**Deskripsi:** Mulai mengerjakan card

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `card_id` (string, required) - ID card

**Success Response (200):**
```json
{
  "message": "Card dimulai",
  "card": {
    "id": "card_001",
    "title": "Design Homepage",
    "description": "Membuat desain homepage baru",
    "priority": "high",
    "status": "in_progress",
    "board": {
      "id": "board_001",
      "name": "To Do"
    },
    "assigned_user": {
      "id": "user_123",
      "name": "John Doe"
    },
    "subtasks_count": 3,
    "completed_subtasks": 1,
    "active_assignment": {
      "id": "assign_001",
      "status": "active",
      "assigned_at": "2025-11-07T10:00:00Z",
      "started_at": "2025-11-07T10:30:00Z",
      "completed_at": null,
      "duration_minutes": 0,
      "duration_hours": 0,
      "human_duration": "0 seconds",
      "is_overdue": false
    },
    "created_at": "2025-11-01T10:00:00Z",
    "updated_at": "2025-11-07T10:31:00Z"
  }
}
```

---

#### 6. Get Card Assignment
**Endpoint:** `GET /cards/{card_id}/assignment`

**Deskripsi:** Mendapatkan assignment card

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `card_id` (string, required) - ID card

**Success Response (200):**
```json
{
  "assignment": {
    "id": "assign_001",
    "status": "active",
    "assigned_at": "2025-11-07T10:00:00Z",
    "started_at": "2025-11-07T10:05:00Z",
    "completed_at": null,
    "duration_minutes": 25,
    "duration_hours": 0.42,
    "human_duration": "25 minutes",
    "is_overdue": false
  }
}
```

---

### Subtasks

#### 1. Create Subtask
**Endpoint:** `POST /subtasks/{card_id}/create`

**Deskripsi:** Buat subtask baru

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**URL Parameters:**
- `card_id` (string, required) - ID card

**Request Body:**
```json
{
  "title": "Create Wireframes",
  "description": "Membuat wireframe untuk halaman utama"
}
```

**Success Response (201):**
```json
{
  "message": "Subtask berhasil dibuat",
  "subtask": {
    "id": "subtask_001",
    "title": "Create Wireframes",
    "description": "Membuat wireframe untuk halaman utama",
    "status": "pending",
    "priority": null,
    "card_id": "card_001",
    "created_at": "2025-11-07T11:00:00Z",
    "updated_at": "2025-11-07T11:00:00Z"
  }
}
```

---

#### 2. Get Subtask Detail
**Endpoint:** `GET /subtasks/{subtask_id}`

**Deskripsi:** Mendapatkan detail subtask

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `subtask_id` (string, required) - ID subtask

**Success Response (200):**
```json
{
  "subtask": {
    "id": "subtask_001",
    "title": "Create Wireframes",
    "description": "Membuat wireframe untuk halaman utama",
    "status": "in_progress",
    "priority": null,
    "card_id": "card_001",
    "created_at": "2025-11-07T11:00:00Z",
    "updated_at": "2025-11-07T11:05:00Z",
    "time_logs": [
      {
        "id": "log_001",
        "start_time": "2025-11-07T11:05:00Z",
        "end_time": null,
        "duration_minutes": null
      }
    ],
    "total_duration_minutes": 0
  }
}
```

---

#### 3. Update Subtask
**Endpoint:** `PUT /subtasks/{subtask_id}`

**Deskripsi:** Update subtask

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**URL Parameters:**
- `subtask_id` (string, required) - ID subtask

**Request Body:**
```json
{
  "title": "Create Wireframes - Updated",
  "description": "Membuat wireframe untuk halaman utama dan halaman produk"
}
```

**Success Response (200):**
```json
{
  "message": "Subtask berhasil diupdate",
  "subtask": {
    "id": "subtask_001",
    "title": "Create Wireframes - Updated",
    "description": "Membuat wireframe untuk halaman utama dan halaman produk",
    "status": "in_progress",
    "priority": null,
    "card_id": "card_001",
    "created_at": "2025-11-07T11:00:00Z",
    "updated_at": "2025-11-07T11:10:00Z"
  }
}
```

---

#### 4. Delete Subtask
**Endpoint:** `DELETE /subtasks/{subtask_id}`

**Deskripsi:** Hapus subtask

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `subtask_id` (string, required) - ID subtask

**Success Response (200):**
```json
{
  "message": "Subtask berhasil dihapus"
}
```

---

#### 5. Start Subtask
**Endpoint:** `POST /subtasks/{subtask_id}/start`

**Deskripsi:** Mulai mengerjakan subtask (Start time tracking)

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `subtask_id` (string, required) - ID subtask

**Success Response (200):**
```json
{
  "message": "Subtask dimulai",
  "subtask": {
    "id": "subtask_001",
    "title": "Create Wireframes",
    "description": "Membuat wireframe untuk halaman utama",
    "status": "in_progress",
    "priority": null,
    "card_id": "card_001",
    "created_at": "2025-11-07T11:00:00Z",
    "updated_at": "2025-11-07T11:05:00Z"
  }
}
```

---

#### 6. Pause Subtask
**Endpoint:** `POST /subtasks/{subtask_id}/pause`

**Deskripsi:** Pause subtask yang sedang dikerjakan

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `subtask_id` (string, required) - ID subtask

**Success Response (200):**
```json
{
  "message": "Subtask dijeda",
  "subtask": {
    "id": "subtask_001",
    "title": "Create Wireframes",
    "description": "Membuat wireframe untuk halaman utama",
    "status": "paused",
    "priority": null,
    "card_id": "card_001",
    "created_at": "2025-11-07T11:00:00Z",
    "updated_at": "2025-11-07T11:15:00Z"
  }
}
```

---

#### 7. Resume Subtask
**Endpoint:** `POST /subtasks/{subtask_id}/resume`

**Deskripsi:** Resume subtask yang sudah dijeda

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `subtask_id` (string, required) - ID subtask

**Success Response (200):**
```json
{
  "message": "Subtask dilanjutkan",
  "subtask": {
    "id": "subtask_001",
    "title": "Create Wireframes",
    "description": "Membuat wireframe untuk halaman utama",
    "status": "in_progress",
    "priority": null,
    "card_id": "card_001",
    "created_at": "2025-11-07T11:00:00Z",
    "updated_at": "2025-11-07T11:20:00Z"
  }
}
```

---

#### 8. Submit Subtask
**Endpoint:** `POST /subtasks/{subtask_id}/submit`

**Deskripsi:** Submit subtask (selesaikan dan tunggu review)

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**URL Parameters:**
- `subtask_id` (string, required) - ID subtask

**Request Body:**
```json
{
  "notes": "Wireframes sudah selesai dan siap untuk review"
}
```

**Success Response (200):**
```json
{
  "message": "Subtask disubmit",
  "subtask": {
    "id": "subtask_001",
    "title": "Create Wireframes",
    "description": "Membuat wireframe untuk halaman utama",
    "status": "review",
    "priority": null,
    "card_id": "card_001",
    "created_at": "2025-11-07T11:00:00Z",
    "updated_at": "2025-11-07T11:30:00Z"
  }
}
```

---

### Time Logs

#### 1. Get All Time Logs
**Endpoint:** `GET /time-logs`

**Deskripsi:** Ambil time logs user (semua subtask)

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `card_id` (integer, optional) - Filter berdasarkan card
- `limit` (integer, optional) - Jumlah limit (default: 50)

**Example URL:** `GET /time-logs?limit=20`

**Success Response (200):**
```json
{
  "time_logs": [
    {
      "id": "log_001",
      "start_time": "2025-11-07T11:05:00Z",
      "end_time": "2025-11-07T11:35:00Z",
      "duration_minutes": 30
    },
    {
      "id": "log_002",
      "start_time": "2025-11-07T14:00:00Z",
      "end_time": null,
      "duration_minutes": null
    }
  ]
}
```

---

#### 2. Get Subtask Time Logs
**Endpoint:** `GET /time-logs/subtask/{subtask_id}`

**Deskripsi:** Ambil time logs untuk subtask tertentu

**Headers:**
```
Authorization: Bearer {token}
```

**URL Parameters:**
- `subtask_id` (string, required) - ID subtask

**Success Response (200):**
```json
{
  "time_logs": [
    {
      "id": "log_001",
      "start_time": "2025-11-07T11:05:00Z",
      "end_time": "2025-11-07T11:35:00Z",
      "duration_minutes": 30
    }
  ]
}
```

---

### Member Dashboard

#### 1. Get Dashboard
**Endpoint:** `GET /member/dashboard`

**Deskripsi:** Ambil dashboard data untuk member

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "dashboard": {
    "total_cards": 10,
    "cards_in_progress": 3,
    "cards_completed": 5,
    "cards_pending": 2,
    "total_subtasks": 25,
    "subtasks_completed": 12,
    "recent_cards": [
      {
        "id": "card_001",
        "title": "Design Homepage",
        "status": "in_progress",
        "priority": "high",
        "board": "To Do",
        "created_at": "2025-11-07T10:00:00Z"
      }
    ],
    "recent_subtasks": [
      {
        "id": "subtask_001",
        "title": "Create Wireframes",
        "status": "in_progress",
        "card_title": "Design Homepage",
        "updated_at": "2025-11-07T11:00:00Z"
      }
    ]
  }
}
```

---

#### 2. Get Statistics
**Endpoint:** `GET /member/statistics`

**Deskripsi:** Ambil statistik member

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `period` (string, optional) - Periode: `today`, `week`, `month`, `all` (default: `month`)

**Example URL:** `GET /member/statistics?period=month`

**Success Response (200):**
```json
{
  "statistics": {
    "period": "month",
    "total_duration_minutes": 1250,
    "total_duration_hours": 20.83,
    "total_duration_days": 0.87,
    "completed_subtasks": 12,
    "completed_cards": 5,
    "average_duration_per_subtask": 104.17,
    "daily_breakdown": [
      {
        "date": "2025-11-07",
        "duration_minutes": 150,
        "duration_hours": 2.5
      },
      {
        "date": "2025-11-06",
        "duration_minutes": 120,
        "duration_hours": 2.0
      }
    ]
  }
}
```

---

## HTTP Status Codes

| Code | Status | Meaning | Action |
|------|--------|---------|--------|
| 200 | ✅ OK | Request berhasil | Process response |
| 201 | ✅ Created | Resource berhasil dibuat | Process response |
| 400 | ❌ Bad Request | Request tidak valid | Check request format |
| 401 | ❌ Unauthorized | Token tidak valid/expired | Login ulang |
| 403 | ❌ Forbidden | Akses ditolak | Check permissions |
| 404 | ❌ Not Found | Resource tidak ditemukan | Check ID |
| 422 | ❌ Unprocessable Entity | Data validation gagal | Check error message |
| 500 | ❌ Internal Server Error | Error di server | Contact admin |

---

## Postman Setup

### Prerequisites

- Postman sudah terinstall (https://www.postman.com/)
- Laravel server running di http://localhost:8000
- Database setup dengan data user

### Step-by-Step Setup

#### Step 1: Setup Postman Environment Manual

**Option A: Manual Setup**

1. Buka Postman
2. Klik tombol **"Environment"** (left sidebar)
3. Klik **"Create New Environment"** atau **"+"**
4. Beri nama: `UKK API Environment`
5. Tambahkan variables:

| Variable | Value | Type |
|----------|-------|------|
| `base_url` | `http://localhost:8000/api/v1` | String |
| `token` | `` (kosong, diisi setelah login) | String |
| `user_id` | `` (kosong) | String |
| `project_id` | `proj_001` | String |
| `card_id` | `card_001` | String |
| `board_id` | `board_001` | String |
| `subtask_id` | `subtask_001` | String |

6. Klik **"Save"**

#### Step 2: Create Collection

1. Di Postman, klik **"Collections"** (left sidebar)
2. Klik **"+"** atau **"Create Collection"**
3. Beri nama: `UKK Project Management API`
4. Klik **"Create"**

#### Step 3: Add Requests ke Collection

Sekarang buat folder dan request sesuai struktur di bawah:

**Struktur Collection:**
```
📁 UKK Project Management API
├── 📁 Authentication
│   ├── POST Login
│   ├── POST Register
│   ├── POST Logout
│   ├── GET Get Profile
│   └── PUT Update Profile
├── 📁 Projects
│   ├── GET Get All Projects
│   ├── GET Get Project Detail
│   └── GET Get Project Boards
├── 📁 Cards
│   ├── GET Get All Cards
│   ├── GET Get Card Detail
│   ├── POST Start Card
│   └── GET Get Card Assignment
├── 📁 Subtasks
│   ├── POST Create Subtask
│   ├── GET Get Subtask Detail
│   ├── POST Start Subtask
│   ├── POST Pause Subtask
│   ├── POST Resume Subtask
│   └── POST Submit Subtask
├── 📁 Time Logs
│   ├── GET Get All Time Logs
│   └── GET Get Subtask Time Logs
└── 📁 Member Dashboard
    ├── GET Get Dashboard
    └── GET Get Statistics
```

#### Step 4: Test Login

1. Buat request baru: **POST** `/auth/login`
2. URL: `{{base_url}}/auth/login`
3. Headers:
   ```
   Content-Type: application/json
   ```
4. Body (raw JSON):
   ```json
   {
     "email": "user@example.com",
     "password": "password123"
   }
   ```
5. Klik **"Send"**
6. Response akan berisi token
7. Copy token → Paste ke environment variable `{{token}}`

#### Step 5: Test Endpoint Lain

Sekarang bisa test endpoint lain:

1. Buka request: `GET /auth/me`
2. URL: `{{base_url}}/auth/me`
3. Headers:
   ```
   Authorization: Bearer {{token}}
   ```
4. Klik **"Send"**
5. Token sudah terinclude otomatis dari environment variable

---

## Contoh Workflow

### Workflow 1: Login & View Dashboard

```
1️⃣ Login
   POST /auth/login
   Body: {"email": "...", "password": "..."}
   Response: Get token ✅

2️⃣ Get Dashboard
   GET /member/dashboard
   Headers: Authorization: Bearer {token}
   Response: Dashboard data ✅

3️⃣ Get Statistics
   GET /member/statistics?period=month
   Response: Statistics data ✅
```

---

### Workflow 2: View Projects & Cards

```
1️⃣ Get All Projects
   GET /projects
   Response: List projects ✅

2️⃣ Get Project Detail
   GET /projects/{project_id}
   Response: Project detail ✅

3️⃣ Get Project Boards
   GET /projects/{project_id}/boards
   Response: Boards in project ✅

4️⃣ Get Cards by Board
   GET /boards/{board_id}/cards
   Response: Cards in board ✅
```

---

### Workflow 3: Complete Subtask (Time Tracking)

```
1️⃣ Get All Cards
   GET /cards
   Response: List cards ✅

2️⃣ Start Card
   POST /cards/{card_id}/start
   Response: Card status = in_progress ✅

3️⃣ Create Subtask
   POST /subtasks/{card_id}/create
   Body: {"title": "...", "description": "..."}
   Response: Subtask created ✅

4️⃣ Start Subtask (⏱️ Time tracking started)
   POST /subtasks/{subtask_id}/start
   Response: Time log started ✅

5️⃣ [Optional] Pause
   POST /subtasks/{subtask_id}/pause
   Response: Time paused ✅

6️⃣ [Optional] Resume
   POST /subtasks/{subtask_id}/resume
   Response: Time resumed ✅

7️⃣ Submit Subtask
   POST /subtasks/{subtask_id}/submit
   Body: {"notes": "..."}
   Response: Status = review ✅

8️⃣ Check Time Spent
   GET /time-logs/subtask/{subtask_id}
   Response: [{"start_time": "...", "end_time": "...", "duration_minutes": ...}] ✅
```

---

## Troubleshooting

### Error 401 - Unauthorized

**Problem:** 
```
{
  "message": "Unauthenticated"
}
```

**Causes:**
- Token tidak ada di header
- Token sudah expired
- Token tidak valid

**Solutions:**
1. Pastikan Authorization header ada: `Authorization: Bearer {token}`
2. Login ulang untuk dapatkan token baru
3. Copy token dari login response & paste ke environment variable

---

### Error 403 - Forbidden

**Problem:**
```
{
  "message": "Akses ditolak"
}
```

**Causes:**
- User bukan member project
- User tidak punya akses ke resource

**Solutions:**
1. Verify user sudah add sebagai member project
2. Check permissions di database
3. Confirm user_id cocok di token

---

### Error 404 - Not Found

**Problem:**
```
{
  "message": "Resource tidak ditemukan"
}
```

**Causes:**
- ID tidak ada di database
- ID salah/typo
- Resource sudah dihapus

**Solutions:**
1. Verify ID dari previous response
2. Check database: select * from {table} where id = '{id}'
3. Pastikan resource sudah ada

---

### Error 422 - Validation Error

**Problem:**
```
{
  "errors": {
    "email": ["Email sudah terdaftar"]
  }
}
```

**Causes:**
- Field validation gagal
- Data tidak sesuai format
- Required field kosong

**Solutions:**
1. Baca error message untuk setiap field
2. Fix data sesuai requirement
3. Cek format: email, phone, etc

---

### Request Hang / Timeout

**Problem:**
- Request tidak ada response
- Loading forever

**Causes:**
- Server tidak running
- Network connection error
- Server crashed

**Solutions:**
1. Check server: `php artisan serve`
2. Check database connection
3. Check network/firewall
4. Restart server & try again

---

## Data Models Reference

### User Object
```json
{
  "id": "user_123",
  "name": "John Doe",
  "email": "john@example.com",
  "role": "member",
  "created_at": "2025-11-07T10:00:00Z"
}
```

### Project Object
```json
{
  "id": "proj_001",
  "name": "Website Redesign",
  "description": "Redesign website kami",
  "status": "active",
  "created_at": "2025-10-01T10:00:00Z",
  "creator": {
    "id": "user_456",
    "name": "Admin User"
  },
  "members_count": 5,
  "boards_count": 3
}
```

### Card Object
```json
{
  "id": "card_001",
  "title": "Design Homepage",
  "description": "Membuat desain homepage baru",
  "priority": "high",
  "status": "in_progress",
  "board": {
    "id": "board_001",
    "name": "To Do"
  },
  "assigned_user": {
    "id": "user_123",
    "name": "John Doe"
  },
  "subtasks_count": 3,
  "completed_subtasks": 1,
  "active_assignment": {
    "id": "assign_001",
    "status": "active",
    "assigned_at": "2025-11-07T10:00:00Z",
    "started_at": "2025-11-07T10:05:00Z",
    "completed_at": null,
    "duration_minutes": 25,
    "duration_hours": 0.42,
    "human_duration": "25 minutes",
    "is_overdue": false
  },
  "created_at": "2025-11-01T10:00:00Z",
  "updated_at": "2025-11-07T10:30:00Z"
}
```

### Subtask Object
```json
{
  "id": "subtask_001",
  "title": "Create Wireframes",
  "description": "Membuat wireframe untuk halaman utama",
  "status": "in_progress",
  "priority": null,
  "card_id": "card_001",
  "created_at": "2025-11-07T11:00:00Z",
  "updated_at": "2025-11-07T11:05:00Z"
}
```

### TimeLog Object
```json
{
  "id": "log_001",
  "start_time": "2025-11-07T11:05:00Z",
  "end_time": "2025-11-07T11:35:00Z",
  "duration_minutes": 30
}
```

### Dashboard Object
```json
{
  "total_cards": 10,
  "cards_in_progress": 3,
  "cards_completed": 5,
  "cards_pending": 2,
  "total_subtasks": 25,
  "subtasks_completed": 12,
  "recent_cards": [...],
  "recent_subtasks": [...]
}
```

### Statistics Object
```json
{
  "period": "month",
  "total_duration_minutes": 1250,
  "total_duration_hours": 20.83,
  "total_duration_days": 0.87,
  "completed_subtasks": 12,
  "completed_cards": 5,
  "average_duration_per_subtask": 104.17,
  "daily_breakdown": [...]
}
```

---

## Testing Checklist

- [ ] Setup Postman environment
- [ ] Test login endpoint
- [ ] Get profile dengan token
- [ ] Update profile
- [ ] List projects
- [ ] Get project detail
- [ ] List cards
- [ ] Get card detail
- [ ] Start card
- [ ] Create subtask
- [ ] Get subtask detail
- [ ] Update subtask
- [ ] Start subtask (time tracking)
- [ ] Pause subtask
- [ ] Resume subtask
- [ ] Submit subtask
- [ ] Get time logs
- [ ] Get subtask time logs
- [ ] Get dashboard
- [ ] Get statistics
- [ ] Test 401 error (remove token)
- [ ] Test 403 error (access denied)
- [ ] Test 404 error (wrong ID)
- [ ] Test 422 error (validation)

---

## Quick Reference

### Base URL
```
http://localhost:8000/api/v1
```

### Required Headers
```
Content-Type: application/json
Authorization: Bearer {token}  (semua endpoint kecuali login & register)
```

### Total Endpoints: 29
- Authentication: 5
- Projects: 3
- Cards: 6
- Subtasks: 8
- Time Logs: 2
- Member Dashboard: 2

### Postman Environment Variables
```
{{base_url}}    = http://localhost:8000/api/v1
{{token}}       = [dari login response]
{{user_id}}     = [dari login response]
{{project_id}}  = proj_001
{{card_id}}     = card_001
{{board_id}}    = board_001
{{subtask_id}}  = subtask_001
```

---

## Summary

✅ **29 Endpoints** siap untuk testing  
✅ **Complete Documentation** dengan examples  
✅ **Workflow Examples** untuk common use cases  
✅ **Error Handling** guide lengkap  
✅ **Troubleshooting** untuk masalah umum  
✅ **Data Models** reference  

**Status:** 🟢 Production Ready

---

*Generated: 7 November 2025*  
*API Version: 1.0.0*  
*Documentation: Complete*
