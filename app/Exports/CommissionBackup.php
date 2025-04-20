<?php
namespace App\Exports;

use App\Models\PartnerCommission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PartnerCommissionExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading
{
    private $from_date;
    private $to_date;
    private $partner;
    private $parent;
    private $type;

    // Constructor to accept filters
    public function __construct($from_date, $to_date, $partner, $parent, $type)
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->partner = $partner;
        $this->parent = $parent;
        $this->type = $type;
    }

    /**
     * Query to fetch data in chunks
     */
    public function query()
    {
        $query = PartnerCommission::where('status', 1);

        if (!empty($this->from_date) && !empty($this->to_date)) {
            $query->whereDate('created_at', '>=', $this->from_date)
                  ->whereDate('created_at', '<=', $this->to_date);
        }

        if (!empty($this->partner)) {
            $query->where('api_id', $this->partner);
        }

        if (!empty($this->parent)) {
            $query->where('from_id', $this->parent);
        }

        if ($this->type !== null) {
            $query->where('type', $this->type);
        }

        return $query->orderByDesc('created_at');
    }

    /**
     * Map each row of data to the format required for export
     */
    public function map($row): array
    {
        $apis = DB::table('apis')->pluck('name', 'id')->toArray();

        $type = $row->type == 1 ? "Deposit" : ($row->type == 2 ? "Withdrawal" : "Unknown");

        return [
            $apis[$row->api_id] ?? 'Unknown',
            $type,
            number_format($row->amount, 2, '.', ','),
            number_format($row->charges, 2, '.', ','),
            number_format($row->total_amount, 2, '.', ','),
            number_format($row->profit, 2, '.', ','),
            $apis[$row->fromapi->id] ?? 'Unknown',
            $row->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'Partner/Agent',
            'Type',
            'Amount',
            'Charges',
            'Net Amount',
            'Profit',
            'Parent',
            'Created At',
        ];
    }

    /**
     * Chunk size for processing records
     */
    public function chunkSize(): int
    {
        return 2000; // Process 1000 records at a time
    }
}
