<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Enums\Role;
use App\Enums\WorkJobStatus;
use App\Models\Appointment;
use App\Models\DentalCase;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Models\Review;
use App\Models\User;
use App\Models\WorkJob;
use App\Models\WorkJobStatusHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Fixed demo accounts ───────────────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name'     => 'Admin User',
            'password' => Hash::make('password'),
            'role'     => Role::Admin,
        ]);

        $doctor1 = User::firstOrCreate(['email' => 'doctor@example.com'], [
            'name'     => 'Dr. Elena Popescu',
            'password' => Hash::make('password'),
            'role'     => Role::Doctor,
        ]);

        $doctor2 = User::firstOrCreate(['email' => 'doctor2@example.com'], [
            'name'     => 'Dr. Mihai Ionescu',
            'password' => Hash::make('password'),
            'role'     => Role::Doctor,
        ]);

        $tech1 = User::firstOrCreate(['email' => 'technician@example.com'], [
            'name'     => 'Andrei Luca',
            'password' => Hash::make('password'),
            'role'     => Role::Technician,
        ]);

        $tech2 = User::firstOrCreate(['email' => 'tech2@example.com'], [
            'name'     => 'Ioana Constantin',
            'password' => Hash::make('password'),
            'role'     => Role::Technician,
        ]);

        $tech3 = User::firstOrCreate(['email' => 'tech3@example.com'], [
            'name'     => 'Radu Gheorghe',
            'password' => Hash::make('password'),
            'role'     => Role::Technician,
        ]);

        // ── Patients ──────────────────────────────────────────────────────────
        $patientData = [
            ['name' => 'Maria Dumitru',     'date_of_birth' => '1985-03-12', 'phone' => '0722 111 222'],
            ['name' => 'Ion Popa',           'date_of_birth' => '1972-07-04', 'phone' => '0744 333 444'],
            ['name' => 'Ana Stoica',         'date_of_birth' => '1990-11-29', 'phone' => '0755 555 666'],
            ['name' => 'Gheorghe Marin',     'date_of_birth' => '1965-01-18', 'phone' => '0766 777 888'],
            ['name' => 'Elena Nistor',       'date_of_birth' => '1993-06-22', 'phone' => '0733 999 000'],
            ['name' => 'Alexandru Vasile',   'date_of_birth' => '1980-09-15', 'phone' => '0722 444 555'],
            ['name' => 'Cristina Ene',       'date_of_birth' => '1978-04-30', 'phone' => '0744 666 777'],
            ['name' => 'Florin Badea',       'date_of_birth' => '1967-12-01', 'phone' => '0755 888 999'],
            ['name' => 'Mihaela Cojocaru',   'date_of_birth' => '1995-02-14', 'phone' => '0766 000 111'],
            ['name' => 'Sorin Toma',         'date_of_birth' => '1974-08-07', 'phone' => '0733 222 333'],
            ['name' => 'Laura Andrei',       'date_of_birth' => '1988-05-19', 'phone' => '0722 555 666'],
            ['name' => 'Bogdan Moldovan',    'date_of_birth' => '1969-10-25', 'phone' => '0744 777 888'],
        ];

        $patients = [];
        foreach ($patientData as $data) {
            $patients[] = Patient::create($data);
        }

        // ── Inventory ─────────────────────────────────────────────────────────
        InventoryItem::insert([
            ['name' => 'Dental Ceramic',      'quantity' => 40, 'unit' => 'g',    'category' => 'Materials',   'low_stock_threshold' => 10, 'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Zirconia Discs',       'quantity' => 12, 'unit' => 'pcs',  'category' => 'Materials',   'low_stock_threshold' => 5,  'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Impression Material',  'quantity' => 3,  'unit' => 'pack', 'category' => 'Consumables', 'low_stock_threshold' => 5,  'description' => 'Low stock — order soon', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dental Wax',           'quantity' => 25, 'unit' => 'g',    'category' => 'Consumables', 'low_stock_threshold' => 10, 'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Acrylic Resin',        'quantity' => 0,  'unit' => 'ml',   'category' => 'Materials',   'low_stock_threshold' => 20, 'description' => 'Out of stock', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Polishing Paste',      'quantity' => 8,  'unit' => 'box',  'category' => 'Tools',       'low_stock_threshold' => 3,  'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cobalt-Chrome Alloy',  'quantity' => 15, 'unit' => 'g',    'category' => 'Materials',   'low_stock_threshold' => 5,  'description' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Articulating Paper',   'quantity' => 6,  'unit' => 'box',  'category' => 'Consumables', 'low_stock_threshold' => 2,  'description' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Cases and Work Jobs — one per status ──────────────────────────────

        // STATUS: awaiting_acceptance
        $case1 = DentalCase::create([
            'patient_id'  => $patients[0]->id,
            'doctor_id'   => $doctor1->id,
            'title'       => 'Full Crown Restoration',
            'description' => 'Upper-left molar full ceramic crown.',
        ]);
        WorkJob::create([
            'title'         => 'Ceramic Crown — UL6',
            'description'   => 'Full ceramic crown for upper-left molar. Shade A2.',
            'scheduled_at'  => Carbon::now()->addDays(14),
            'status'        => WorkJobStatus::AwaitingAcceptance,
            'doctor_id'     => $doctor1->id,
            'technician_id' => $tech1->id,
            'case_id'       => $case1->id,
        ]);

        // STATUS: in_progress
        $case2 = DentalCase::create([
            'patient_id'  => $patients[1]->id,
            'doctor_id'   => $doctor1->id,
            'title'       => 'Partial Denture',
            'description' => 'Lower partial denture, cobalt-chrome framework.',
        ]);
        $job2 = WorkJob::create([
            'title'         => 'Co-Cr Partial Denture — Lower',
            'description'   => 'Cobalt-chrome partial denture framework with acrylic saddles.',
            'scheduled_at'  => Carbon::now()->addDays(10),
            'status'        => WorkJobStatus::InProgress,
            'doctor_id'     => $doctor1->id,
            'technician_id' => $tech2->id,
            'case_id'       => $case2->id,
        ]);
        $this->addHistory($job2, WorkJobStatus::AwaitingAcceptance, WorkJobStatus::InProgress, $tech2, 'Accepted. Started framework fabrication.');

        // STATUS: in_review
        $case3 = DentalCase::create([
            'patient_id'  => $patients[2]->id,
            'doctor_id'   => $doctor2->id,
            'title'       => 'Implant Crown',
            'description' => 'Screw-retained implant crown, upper right central incisor.',
        ]);
        $job3 = WorkJob::create([
            'title'         => 'Implant Crown — UR1',
            'description'   => 'Screw-retained zirconia implant crown on Nobel Active implant.',
            'scheduled_at'  => Carbon::now()->addDays(5),
            'status'        => WorkJobStatus::InReview,
            'doctor_id'     => $doctor2->id,
            'technician_id' => $tech1->id,
            'case_id'       => $case3->id,
        ]);
        $this->addHistory($job3, WorkJobStatus::AwaitingAcceptance, WorkJobStatus::InProgress, $tech1, 'Accepted. Scanning and design started.');
        $this->addHistory($job3, WorkJobStatus::InProgress, WorkJobStatus::InReview, $tech1, 'Crown milled and polished. Ready for doctor check.');

        // STATUS: needs_revision
        $case4 = DentalCase::create([
            'patient_id'  => $patients[3]->id,
            'doctor_id'   => $doctor2->id,
            'title'       => 'Veneer Set',
            'description' => 'Six upper anterior ceramic veneers.',
        ]);
        $job4 = WorkJob::create([
            'title'         => 'Ceramic Veneers — Upper Anteriors',
            'description'   => 'Six porcelain veneers UR3–UL3, shade BL2.',
            'scheduled_at'  => Carbon::now()->addDays(7),
            'status'        => WorkJobStatus::NeedsRevision,
            'doctor_id'     => $doctor2->id,
            'technician_id' => $tech3->id,
            'case_id'       => $case4->id,
        ]);
        $this->addHistory($job4, WorkJobStatus::AwaitingAcceptance, WorkJobStatus::InProgress, $tech3, 'Accepted and started.');
        $this->addHistory($job4, WorkJobStatus::InProgress, WorkJobStatus::InReview, $tech3, 'All six veneers finished and polished.');
        $this->addHistory($job4, WorkJobStatus::InReview, WorkJobStatus::NeedsRevision, $doctor2, 'Shade on UL2 is too dark — please adjust to BL1.');

        // STATUS: ready_for_delivery
        $case5 = DentalCase::create([
            'patient_id'  => $patients[4]->id,
            'doctor_id'   => $doctor1->id,
            'title'       => 'Occlusal Splint',
            'description' => 'Hard acrylic night guard, upper arch, bruxism management.',
        ]);
        $job5 = WorkJob::create([
            'title'         => 'Hard Acrylic Night Guard',
            'description'   => 'Hard acrylic occlusal splint, upper arch. Balanced occlusion required.',
            'scheduled_at'  => Carbon::now()->addDays(2),
            'status'        => WorkJobStatus::ReadyForDelivery,
            'doctor_id'     => $doctor1->id,
            'technician_id' => $tech2->id,
            'case_id'       => $case5->id,
        ]);
        $this->addHistory($job5, WorkJobStatus::AwaitingAcceptance, WorkJobStatus::InProgress, $tech2, 'Accepted. Stone model poured.');
        $this->addHistory($job5, WorkJobStatus::InProgress, WorkJobStatus::InReview, $tech2, 'Splint polished and trimmed.');
        $this->addHistory($job5, WorkJobStatus::InReview, WorkJobStatus::ReadyForDelivery, $doctor1, 'Checked — good fit on model. Approved for delivery.');

        // STATUS: delivered (+ review)
        $case6 = DentalCase::create([
            'patient_id'  => $patients[5]->id,
            'doctor_id'   => $doctor2->id,
            'title'       => 'Full Arch Bridge',
            'description' => 'Four-unit zirconia bridge, upper right quadrant.',
        ]);
        $job6 = WorkJob::create([
            'title'         => 'Zirconia Bridge — UR3–UR6',
            'description'   => 'Four-unit full-contour zirconia bridge. Shade A3. Digital workflow.',
            'scheduled_at'  => Carbon::now()->subDays(2),
            'status'        => WorkJobStatus::Delivered,
            'doctor_id'     => $doctor2->id,
            'technician_id' => $tech3->id,
            'case_id'       => $case6->id,
        ]);
        $this->addHistory($job6, WorkJobStatus::AwaitingAcceptance, WorkJobStatus::InProgress, $tech3, 'Design confirmed. Milling started.');
        $this->addHistory($job6, WorkJobStatus::InProgress, WorkJobStatus::InReview, $tech3, 'Bridge stained, glazed, and polished.');
        $this->addHistory($job6, WorkJobStatus::InReview, WorkJobStatus::ReadyForDelivery, $doctor2, 'Perfect fit on model. Shade match excellent. Approved.');
        $this->addHistory($job6, WorkJobStatus::ReadyForDelivery, WorkJobStatus::Delivered, $doctor2, 'Cemented intra-orally. Patient very happy.');

        Review::create([
            'reviewer_id' => $doctor2->id,
            'reviewee_id' => $tech3->id,
            'work_job_id' => $job6->id,
            'rating'      => 5,
            'comment'     => 'Outstanding craftsmanship on the four-unit bridge. Perfect contour and shade on first attempt.',
            'is_visible'  => true,
        ]);

        // STATUS: cancelled
        $case7 = DentalCase::create([
            'patient_id'  => $patients[6]->id,
            'doctor_id'   => $doctor1->id,
            'title'       => 'Maryland Bridge',
            'description' => 'Resin-bonded bridge, lower left lateral incisor.',
        ]);
        $job7 = WorkJob::create([
            'title'         => 'Maryland Bridge — LL2',
            'description'   => 'Resin-bonded maryland bridge, lower left. Metal wings, porcelain pontic.',
            'scheduled_at'  => Carbon::now()->subDays(10),
            'status'        => WorkJobStatus::Cancelled,
            'doctor_id'     => $doctor1->id,
            'technician_id' => $tech1->id,
            'case_id'       => $case7->id,
        ]);
        $this->addHistory($job7, WorkJobStatus::AwaitingAcceptance, WorkJobStatus::Cancelled, $doctor1, 'Patient chose implant instead. Work cancelled.');

        // BONUS: revision loop — NeedsRevision → InProgress rework cycle visible in audit trail
        $case8 = DentalCase::create([
            'patient_id'  => $patients[7]->id,
            'doctor_id'   => $doctor2->id,
            'title'       => 'Complete Upper Denture',
            'description' => 'Full upper denture, acrylic base with ceramic teeth.',
        ]);
        $job8 = WorkJob::create([
            'title'         => 'Complete Upper Denture',
            'description'   => 'Full upper denture. Acrylic base, ceramic teeth. Three try-in stages required.',
            'scheduled_at'  => Carbon::now()->addDays(8),
            'status'        => WorkJobStatus::InProgress,
            'doctor_id'     => $doctor2->id,
            'technician_id' => $tech1->id,
            'case_id'       => $case8->id,
        ]);
        $this->addHistory($job8, WorkJobStatus::AwaitingAcceptance, WorkJobStatus::InProgress, $tech1, 'Accepted. Primary impressions poured.');
        $this->addHistory($job8, WorkJobStatus::InProgress, WorkJobStatus::InReview, $tech1, 'First wax try-in ready.');
        $this->addHistory($job8, WorkJobStatus::InReview, WorkJobStatus::NeedsRevision, $doctor2, 'Midline shifted 2mm left. Vertical dimension too open — reduce by 1mm.');
        $this->addHistory($job8, WorkJobStatus::NeedsRevision, WorkJobStatus::InProgress, $tech1, 'Acknowledged. Remounting and adjusting.');

        // BONUS: second delivered job so doctor1 also has a completed case history
        $case9 = DentalCase::create([
            'patient_id'  => $patients[8]->id,
            'doctor_id'   => $doctor1->id,
            'title'       => 'Single Posterior Crown',
            'description' => 'Lower right first molar, e.max press crown.',
        ]);
        $job9 = WorkJob::create([
            'title'         => 'e.max Crown — LR6',
            'description'   => 'IPS e.max Press full-coverage crown, lower right first molar. Shade A2.',
            'scheduled_at'  => Carbon::now()->subDays(5),
            'status'        => WorkJobStatus::Delivered,
            'doctor_id'     => $doctor1->id,
            'technician_id' => $tech2->id,
            'case_id'       => $case9->id,
        ]);
        $this->addHistory($job9, WorkJobStatus::AwaitingAcceptance, WorkJobStatus::InProgress, $tech2, 'Started pressing.');
        $this->addHistory($job9, WorkJobStatus::InProgress, WorkJobStatus::InReview, $tech2, 'Crown characterised and glazed.');
        $this->addHistory($job9, WorkJobStatus::InReview, WorkJobStatus::ReadyForDelivery, $doctor1, 'Approved.');
        $this->addHistory($job9, WorkJobStatus::ReadyForDelivery, WorkJobStatus::Delivered, $doctor1, 'Cemented with RelyX. Good occlusion.');

        Review::create([
            'reviewer_id' => $doctor1->id,
            'reviewee_id' => $tech2->id,
            'work_job_id' => $job9->id,
            'rating'      => 4,
            'comment'     => 'Great surface texture. Shade was spot on. Delivery was slightly delayed.',
            'is_visible'  => true,
        ]);

        // ── Appointments ──────────────────────────────────────────────────────
        // Past completed — delivery of the bridge
        Appointment::create([
            'patient_id'   => $patients[5]->id,
            'doctor_id'    => $doctor2->id,
            'work_job_id'  => $job6->id,
            'case_id'      => $case6->id,
            'scheduled_at' => Carbon::now()->subDays(2),
            'status'       => AppointmentStatus::Completed,
            'notes'        => 'Bridge cemented. Patient reviewed post-op care.',
        ]);

        // Upcoming delivery appointment
        Appointment::create([
            'patient_id'   => $patients[4]->id,
            'doctor_id'    => $doctor1->id,
            'work_job_id'  => $job5->id,
            'case_id'      => $case5->id,
            'scheduled_at' => Carbon::now()->addDays(2),
            'status'       => AppointmentStatus::Scheduled,
            'notes'        => 'Night guard delivery and fitting appointment.',
        ]);

        // Past completed — e.max crown delivery
        Appointment::create([
            'patient_id'   => $patients[8]->id,
            'doctor_id'    => $doctor1->id,
            'work_job_id'  => $job9->id,
            'case_id'      => $case9->id,
            'scheduled_at' => Carbon::now()->subDays(5),
            'status'       => AppointmentStatus::Completed,
            'notes'        => 'Crown seated. Bite checked and adjusted.',
        ]);

        // General upcoming appointments
        $upcomingPatients = [$patients[0], $patients[1], $patients[2], $patients[9], $patients[10], $patients[11]];
        foreach ($upcomingPatients as $i => $patient) {
            Appointment::create([
                'patient_id'   => $patient->id,
                'doctor_id'    => $i % 2 === 0 ? $doctor1->id : $doctor2->id,
                'scheduled_at' => Carbon::now()->addDays($i * 3 + 4),
                'status'       => AppointmentStatus::Scheduled,
            ]);
        }

        // Past completed general
        foreach ([$patients[0], $patients[3], $patients[6]] as $i => $patient) {
            Appointment::create([
                'patient_id'   => $patient->id,
                'doctor_id'    => $i % 2 === 0 ? $doctor1->id : $doctor2->id,
                'scheduled_at' => Carbon::now()->subDays($i * 4 + 7),
                'status'       => AppointmentStatus::Completed,
                'notes'        => 'Routine check-up completed.',
            ]);
        }

        // Cancelled appointments
        foreach ([$patients[1], $patients[7]] as $patient) {
            Appointment::create([
                'patient_id'   => $patient->id,
                'doctor_id'    => $doctor1->id,
                'scheduled_at' => Carbon::now()->subDays(3),
                'status'       => AppointmentStatus::Cancelled,
                'notes'        => 'Patient cancelled — rescheduling required.',
            ]);
        }
    }

    private function addHistory(
        WorkJob       $job,
        WorkJobStatus $from,
        WorkJobStatus $to,
        User          $changedBy,
        ?string       $notes = null,
    ): void {
        WorkJobStatusHistory::create([
            'work_job_id' => $job->id,
            'from_status' => $from,
            'to_status'   => $to,
            'changed_by'  => $changedBy->id,
            'notes'       => $notes,
        ]);
    }
}
