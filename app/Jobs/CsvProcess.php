<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Contact;

class CsvProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $header;
    public $fields;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $header, $fields)
    {
        $this->data = $data;
        $this->header = $header;
        $this->fields = $fields;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->data as $row){
            $contact = new Contact();
            foreach (config('app.db_fields') as $index => $field) {
                if ($this->header) {
                    $temp = $row[strtolower($this->fields[str_replace("#", "", $field)])];
                    $contact->$field = strlen($temp) > 0 ? $temp : "";
                } else {
                    $contact->$field = $row[$this->fields[$index]];
                }
            }
            $contact->save();
        }
    }
    
    public function withBatchId() {
        return 1;
    }
}
