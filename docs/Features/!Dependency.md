# 🗺️ Medi_Flow — Development Roadmap

> Build order and dependency map for all project phases.

---

## 📊 Phase Overview

| #  | Phase                      | Depends On     | Status |
|----|----------------------------|----------------|--------|
| 0  | Foundation (Auth & Roles)  | —              | ⬜     |
| 1  | Patient Management         | Phase 0        | ⬜     |
| 2  | Doctors & Availability     | Phase 0, 1     | ⬜     |
| 3  | Appointment Scheduling     | Phase 1, 2     | ⬜     |
| 4  | Electronic Medical Records | Phase 1, 2, 3  | ⬜     |
| 5  | Prescriptions              | Phase 1, 3, 4  | ⬜     |
| 6  | Billing & Insurance        | Phase 1, 3     | ⬜     |
| 7  | Notifications              | Phase 3, 6     | ⬜     |
| 8  | Dashboards & Reports       | Phase 1, 3, 6  | ⬜     |
| 9  | Testing                    | All above      | ⬜     |
| 10 | Deployment                 | Phase 9        | ⬜     |

> **Legend:** ⬜ Not Started &nbsp;·&nbsp; 🔨 In Progress &nbsp;·&nbsp; ✅ Done

---

## 🔗 Dependency Flow

```
PHASE 0  Foundation
  │
  ▼
PHASE 1  Patients ──────────────────────────────┐
  │                                              │
  ▼                                              │
PHASE 2  Doctors & Availability                  │
  │                                              │
  ▼                                              │
PHASE 3  Appointments ──────────┐                │
  │                             │                │
  ▼                             ▼                ▼
PHASE 4  EMR               PHASE 5  Rx     PHASE 6  Billing
  │                             │                │
  └──────────┬──────────────────┘                │
             ▼                                   │
        PHASE 7  Notifications                   │
             │                                   │
             └───────────┬───────────────────────┘
                         ▼
                    PHASE 8  Dashboards
                         │
                         ▼
                    PHASE 9  Testing
                         │
                         ▼
                    PHASE 10 Deployment
```

---

## 📂 Quick Links

| Doc | Feature |
|-----|---------|
| [1_Patient.md](1_Patient.md)       | Patient Management         |
| [2_Available.md](2_Available.md)   | Doctors & Availability     |
| [3_Scheduling.md](3_Scheduling.md) | Appointment Scheduling     |
| [4_EMR.md](4_EMR.md)              | Electronic Medical Records |
| [6_Billing.md](6_Billing.md)      | Billing & Insurance        |
