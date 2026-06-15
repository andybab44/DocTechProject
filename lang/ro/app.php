<?php

return [

    // Navigation
    'nav' => [
        'calendar'     => 'Calendar',
        'users'        => 'Utilizatori',
        'licenses'     => 'Licențe',
        'inventory'    => 'Inventar',
        'appointments' => 'Programări',
        'reviews'      => 'Recenzii',
        'logout'       => 'Deconectare',
    ],

    // Auth
    'auth' => [
        'sign_in'         => 'Autentificare în :app',
        'email'           => 'Adresă de e-mail',
        'password'        => 'Parolă',
        'remember_me'     => 'Ține-mă minte',
        'sign_in_button'  => 'Autentificare',
    ],

    // Dashboard
    'dashboard' => [
        'admin'       => 'Panou de administrare',
        'doctor'      => 'Panou medic',
        'technician'  => 'Panou tehnician',
        'welcome'     => 'Bine ați revenit, :name.',
        'welcome_dr'  => 'Bine ați revenit, Dr. :name.',
        'access_full' => 'Aveți acces complet.',

        'role'        => 'Rol',
        'access_level'=> 'Nivel de acces',
        'status'      => 'Status',
        'active'      => 'Activ',
        'full'        => 'Complet',
        'clinical'    => 'Clinic',
        'technical'   => 'Tehnic',

        'manage_users'       => 'Gestionare utilizatori',
        'manage_users_desc'  => 'Adăugați, vizualizați și gestionați conturile de utilizatori',
        'view_calendar'      => '📅 Vezi calendarul',
        'view_my_calendar'   => '📅 Vezi calendarul lucrărilor mele',
        'new_work_job'       => '+ Lucrare nouă',
    ],

    // Work Jobs – Calendar
    'calendar' => [
        'title'     => 'Lucrări — :month',
        'new_job'   => '+ Lucrare nouă',
        'add_job'   => 'Adaugă lucrare',
        'mon' => 'Lun', 'tue' => 'Mar', 'wed' => 'Mie', 'thu' => 'Joi',
        'fri' => 'Vin', 'sat' => 'Sâm', 'sun' => 'Dum',
    ],

    // Work Jobs – Form (shared create/edit)
    'work_job' => [
        'back_calendar'     => '← Înapoi la calendar',
        'back'              => '← Înapoi',
        'new_title'         => 'Lucrare nouă',
        'edit_title'        => 'Editare lucrare',

        'field_title'       => 'Titlu',
        'field_description' => 'Descriere',
        'field_datetime'    => 'Dată și oră',
        'field_technician'  => 'Atribuiți tehnician',
        'select_technician' => '— Selectați un tehnician —',
        'field_status'      => 'Status',

        'attachments'          => 'Atașamente',
        'no_attachments'       => 'Niciun fișier atașat.',
        'attach_file'          => 'Atașează fișier',
        'upload'               => 'Încarcă',
        'drop_or_browse'       => ':link sau trageți și fixați',
        'click_to_browse'      => 'Faceți clic pentru a naviga',
        'file_selected'        => 'Fișier selectat — faceți clic pentru a schimba',
        'drop_hint'            => 'PDF, Word, Excel, imagini, ZIP — max 50 MB',
        'drop_hint_create'     => 'PDF, Word, Excel, imagini, ZIP — până la 10 fișiere, max 20 MB fiecare',
        'attachments_optional' => 'Atașamente',
        'attachments_optional_note' => '(opțional)',
        'download'             => 'Descarcă',
        'remove'               => 'Șterge',
        'remove_attachment'    => 'Șterge atașamentul',
        'remove_confirm'       => 'Sigur doriți să ștergeți :name? Această acțiune nu poate fi anulată.',
        'cancel_download'      => 'Anulează',

        'btn_create'    => 'Creează lucrarea',
        'btn_save'      => 'Salvează modificările',
        'btn_cancel'    => 'Anulează',
        'btn_delete'    => 'Șterge',
        'btn_edit'      => 'Editează',
        'btn_upload'    => 'Încarcă',
        'btn_remove'    => 'Șterge',

        'update_status'           => 'Actualizare status',
        'btn_save_status'         => 'Salvează',
        'status_notes_placeholder' => 'Notă opțională despre această schimbare de status…',

        'details_requested_by'  => 'Solicitat de',
        'details_assigned_to'   => 'Atribuit',

        'delete_confirm' => 'Ștergeți această lucrare?',

        'success_created' => 'Lucrarea a fost creată cu succes.',
        'success_uploaded' => 'Fișierul a fost încărcat cu succes.',
        'success_deleted_attachment' => 'Atașamentul a fost șters.',
    ],

    // Admin – Users
    'users' => [
        'title'         => 'Utilizatori',
        'subtitle'      => 'Gestionați toți utilizatorii din sistem.',
        'add_user'      => '+ Adaugă utilizator',
        'add_title'     => 'Adaugă utilizator',
        'edit_title'    => 'Editare utilizator',

        'col_name'      => 'Nume',
        'col_email'     => 'E-mail',
        'col_role'      => 'Rol',
        'col_status'    => 'Status',
        'col_created'   => 'Creat',

        'status_active'   => 'Activ',
        'status_inactive' => 'Inactiv',

        'activate'    => 'Activează',
        'deactivate'  => 'Dezactivează',
        'activate_confirm'   => 'Activați acest utilizator?',
        'deactivate_confirm' => 'Dezactivați acest utilizator?',
        'no_users'    => 'Niciun utilizator găsit.',

        'field_name'                => 'Nume complet',
        'field_email'               => 'Adresă de e-mail',
        'field_role'                => 'Rol',
        'field_password'            => 'Parolă',
        'field_password_new'        => 'Parolă nouă',
        'field_password_keep'       => '(lăsați necompletat pentru a păstra parola actuală)',
        'field_password_confirm'    => 'Confirmare parolă',
        'field_password_new_confirm'=> 'Confirmare parolă nouă',
        'select_role'               => 'Selectați un rol…',

        'btn_create'  => 'Creează utilizator',
        'btn_save'    => 'Salvează modificările',
        'btn_cancel'  => 'Anulează',
        'btn_edit'    => 'Editează',
        'btn_back'    => '← Înapoi',

        'search_placeholder' => 'Caută după nume sau e-mail…',
        'filter_all_roles'   => 'Toate rolurile',
        'btn_filter'         => 'Filtrează',
        'btn_reset'          => 'Resetează',
        'col_jobs_created'   => 'Lucrări create',
        'col_jobs_assigned'  => 'Lucrări atribuite',
        'col_avg_rating'     => 'Rating mediu',
    ],

    // Admin – Licenses
    'licenses' => [
        'title'         => 'Licențe',
        'subtitle'      => 'Gestionați licențele per utilizator.',
        'new_license'   => '+ Licență nouă',
        'create_title'  => 'Licență nouă',
        'edit_title'    => 'Editare licență',

        'col_user'      => 'Utilizator',
        'col_role'      => 'Rol',
        'col_status'    => 'Status',
        'col_modules'   => 'Module',
        'col_expires'   => 'Expiră',
        'col_actions'   => 'Acțiuni',

        'status_active'   => 'Activă',
        'status_inactive' => 'Inactivă',
        'status_expired'  => 'Expirată',
        'status_none'     => 'Fără licență',

        'assign'      => 'Atribuie',
        'edit'        => 'Editează',
        'activate'    => 'Activează',
        'deactivate'  => 'Dezactivează',
        'perpetual'   => 'Nelimitată',

        'field_user'        => 'Utilizator',
        'field_expires'     => 'Dată expirare',
        'field_expires_hint'=> '(lăsați necompletat pentru nelimitată)',
        'field_modules'     => 'Module',
        'no_modules'        => '—',
        'no_users_available'=> 'Toți utilizatorii au deja o licență.',

        'btn_create'  => 'Creează licența',
        'btn_save'    => 'Salvează modificările',
        'btn_cancel'  => 'Anulează',

        'license_status_label'   => 'Status licență',
        'license_status_active'  => 'Această licență este în prezent activă.',
        'license_status_inactive'=> 'Această licență este în prezent inactivă.',
        'back'        => '← Înapoi la licențe',

        'select_user'  => '— Selectați un utilizator —',
    ],

    // Admin – Inventory
    'inventory' => [
        'title'           => 'Inventar',
        'subtitle'        => 'Urmăriți stocurile şi înregistrați utilizarea articolelor.',
        'add_item'        => '+ Adaugă articol',
        'create_title'    => 'Articol nou de inventar',
        'edit_title'      => 'Editare articol de inventar',
        'back_to_list'    => '← Înapoi la inventar',

        'col_name'        => 'Nume',
        'col_category'    => 'Categorie',
        'col_quantity'    => 'Cantitate',
        'col_unit'        => 'Unitate',
        'col_threshold'   => 'Prag stoc scăzut',
        'col_status'      => 'Status',
        'col_date'        => 'Data',
        'col_used_by'     => 'Utilizat de',
        'col_qty_used'    => 'Cantitate utilizată',
        'col_work_job'    => 'Lucrare',
        'col_notes'       => 'Note',

        'status_in_stock'    => 'In stoc',
        'status_low_stock'   => 'Stoc scăzut',
        'status_out_of_stock'=> 'Stoc epuizat',

        'current_stock'      => 'Stoc curent',
        'low_stock_threshold'=> 'Prag stoc scăzut',
        'total_usages'       => 'Total înregistrări utilizare',
        'restock_title'      => 'Reaprovizionare',
        'log_usage_title'    => 'Înregistrare utilizare',
        'usage_history'      => 'Istoric utilizare',
        'no_items'           => 'Niciun articol de inventar găsit.',
        'no_usage'           => 'Nicio utilizare înregistrată încă.',

        'field_name'         => 'Nume',
        'field_description'  => 'Descriere',
        'field_quantity'     => 'Cantitate inițială',
        'field_unit'         => 'Unitate',
        'field_category'     => 'Categorie',
        'field_threshold'    => 'Prag stoc scăzut',
        'field_quantity_used'=> 'Cantitate utilizată',
        'field_work_job'     => 'Lucrare (opțional)',
        'field_notes'        => 'Note (opțional)',
        'select_work_job'    => '— Selectați o lucrare —',

        'btn_view'           => 'Vizualizează',
        'btn_edit'           => 'Editează',
        'btn_delete'         => 'Şterg',
        'btn_restock'        => 'Reaprovizionare',
        'btn_log_usage'      => 'Înregistrează utilizarea',
        'btn_create'         => 'Creează articol',
        'btn_save'           => 'Salvează modificările',
        'btn_cancel'         => 'Anulează',
        'delete_confirm'     => 'Ştergeți acest articol de inventar? Toate înregistrările de utilizare vor fi, de asemenea, şterse.',

        'success_created'    => 'Articol de inventar creat.',
        'success_updated'    => 'Articol de inventar actualizat.',
        'success_restocked'  => 'Stocul a fost actualizat cu succes.',
        'success_deleted'    => 'Articol de inventar şters.',
        'success_usage_logged' => 'Utilizare înregistrată cu succes.',
        'error_insufficient_stock' => 'Stoc insuficient pentru cantitatea solicitată.',
    ],

    // Common
    'cancel' => 'Anulează',
    'common' => [
        'back'   => '← Înapoi',
        'cancel' => 'Anulează',
        'save'   => 'Salvează modificările',
        'delete' => 'Șterge',
        'edit'   => 'Editează',
        'remove' => 'Șterge',
    ],

    // Notifications
    'hello'                       => 'Bună ziua, :name!',
    'appointment_reminder_subject'=> 'Reminder programare: :patient',
    'appointment_reminder_body'   => 'Acesta este un memento că aveți o programare cu :patient stabilită pentru :datetime.',
    'appointment_reminder_footer' => 'Vă rugăm să vă autentificați în sistem pentru a vedea detalii complete.',
    'view_appointment'            => 'Vizualizați programarea',

    // Patients
    'patients' => [
        'title'          => 'Pacienți',
        'subtitle'       => 'Gestionați fișele pacienților.',
        'add_patient'    => '+ Adaugă pacient',
        'create_title'   => 'Pacient nou',
        'edit_title'     => 'Editare pacient: :name',
        'back'           => 'Înapoi la pacienți',
        'details'        => 'Detalii pacient',
        'appointment_history' => 'Istoricul programărilor',
        'no_appointments' => 'Nicio programare înregistrată.',
        'new_appointment' => '+ Programare nouă',
        'no_patients'    => 'Niciun pacient găsit.',

        'col_name'         => 'Nume',
        'col_dob'          => 'Data nașterii',
        'col_email'        => 'E-mail',
        'col_phone'        => 'Telefon',
        'col_appointments' => 'Programări',

        'field_name'  => 'Nume complet',
        'field_dob'   => 'Data nașterii',
        'field_email' => 'E-mail',
        'field_phone' => 'Telefon',
        'field_notes' => 'Note',

        'btn_view'   => 'Vizualizează',
        'btn_edit'   => 'Editează',
        'btn_delete' => 'Șterge',
        'btn_save'   => 'Salvează',

        'delete_confirm'  => 'Ștergeți acest pacient? Toate programările sale vor fi, de asemenea, șterse.',
        'success_created' => 'Pacient creat.',
        'success_updated' => 'Pacient actualizat.',
        'success_deleted' => 'Pacient șters.',
        'notes'           => 'Note',
    ],

    // Reviews
    'reviews' => [
        'admin_title'              => 'Moderare recenzii',
        'admin_subtitle'           => 'Gestionați toate recenziile trimise.',
        'job_reviews_title'        => 'Recenzii',
        'leave_review_title'       => 'Lăsați o recenzie',
        'already_reviewed'         => 'Ați evaluat această lucrare cu :rating/5.',
        'col_reviewer'             => 'Recenzent',
        'col_reviewee'             => 'Evaluat',
        'col_job'                  => 'Lucrare',
        'col_rating'               => 'Rating',
        'col_comment'              => 'Comentariu',
        'col_status'               => 'Status',
        'col_date'                 => 'Dată',
        'col_avg_rating'           => 'Rating mediu',
        'status_visible'           => 'Vizibil',
        'status_hidden'            => 'Ascuns',
        'field_rating'             => 'Rating',
        'field_comment'            => 'Comentariu (opțional)',
        'btn_submit'               => 'Trimite recenzia',
        'btn_hide'                 => 'Ascunde',
        'btn_show'                 => 'Arată',
        'btn_delete'               => 'Șterge',
        'delete_confirm'           => 'Ștergeți definitiv această recenzie?',
        'no_reviews'               => 'Nicio recenzie găsită.',
        'success_submitted'        => 'Recenzie trimisă.',
        'success_visibility_updated' => 'Vizibilitatea recenziei a fost actualizată.',
        'success_deleted'          => 'Recenzie ștearsă.',
    ],

    // ── Etichete enum ────────────────────────────────────────────────────────
    'work_job_statuses' => [
        'awaiting_acceptance' => 'În așteptare',
        'in_progress'         => 'În lucru',
        'in_review'           => 'În verificare',
        'needs_revision'      => 'Necesită revizuire',
        'ready_for_delivery'  => 'Gata de livrare',
        'delivered'           => 'Livrat',
        'cancelled'           => 'Anulat',
    ],

    'appointment_statuses' => [
        'scheduled' => 'Programat',
        'completed' => 'Finalizat',
        'cancelled' => 'Anulat',
    ],

    'roles' => [
        'admin'       => 'Administrator',
        'doctor'      => 'Doctor',
        'technician'  => 'Tehnician',
    ],

    // Appointments
    'appointments' => [
        'calendar_title'  => 'Programări — :month',
        'show_title'      => 'Programare: :patient',
        'create_title'    => 'Programare nouă',
        'edit_title'      => 'Editare programare: :patient',
        'back_calendar'   => 'Înapoi la calendar',
        'new_appointment' => '+ Programare nouă',
        'add_appointment' => 'Adaugă programare',
        'no_appointments' => 'Nicio programare găsită.',

        'col_date'     => 'Dată și oră',
        'col_doctor'   => 'Medic',
        'col_patient'  => 'Pacient',
        'col_status'   => 'Status',
        'col_work_job' => 'Lucrare',
        'col_notes'    => 'Note',

        'field_patient'      => 'Pacient',
        'field_doctor'       => 'Medic',
        'field_scheduled_at' => 'Dată și oră',
        'field_notes'        => 'Note',

        'select_patient' => '— Selectați un pacient —',
        'select_doctor'  => '— Selectați un medic —',

        'btn_view'     => 'Vizualizează',
        'btn_edit'     => 'Editează',
        'btn_save'     => 'Salvează',
        'btn_cancel'   => 'Anulează programarea',
        'btn_complete' => 'Marchează ca finalizat',

        'cancel_confirm'      => 'Anulați această programare?',
        'error_not_scheduled' => 'Doar programările cu statusul „Programat" pot fi actualizate.',
        'success_created'     => 'Programare creată.',
        'success_updated'     => 'Programare reprogramată.',
        'success_cancelled'   => 'Programare anulată.',
        'success_completed'   => 'Programare marcată ca finalizată.',
    ],
];
