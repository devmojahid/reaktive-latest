<?php

namespace Modules\GlobalSetting\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\GlobalSetting\Database\factories\GlobalSettingFactory;

class GlobalSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'invoice_company_name',
'invoice_company_tax_id',
'invoice_company_reg_no',
'invoice_company_email',
'invoice_company_phone',
'invoice_company_address_line1',
'invoice_company_address_line2',
'invoice_company_country',
'invoice_company_state',
'invoice_company_city',
'invoice_company_zip',
'invoice_company_bank_name',
'invoice_company_iban',
'invoice_company_swift_bic',
'invoice_prefix',
'invoice_due_days',
'invoice_footer_note',

        ];

}
