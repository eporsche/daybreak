<?php

return [
    'exception_message' => '예외 메시지: :메세지',
    'exception_trace' => '예외 추적: :추적',
    'exception_message_title' => '예외 메세지',
    'exception_trace_title' => '예외 추적',

    'backup_failed_subject' => ':application_name 백업 실패',
    'backup_failed_body' => '중요: application_name을(를) 백업하는 동안 오류가 발생했습니다.',

    'backup_successful_subject' => ':application_name의 성공적인 새로운 백업',
    'backup_successful_subject_title' => '새로운 백업에 성공하였습니다!',
    'backup_successful_body' => '좋은 소식입니다. :application_name의 새로운 백업이 :disk_name 디스크에 성공적으로 생성되었습니다.',

    'cleanup_failed_subject' => ':application_name의 백업 정리에 실패했습니다.',
    'cleanup_failed_body' => ':application_name의 백업을 정리하는 동안 오류가 발생했습니다.',

    'cleanup_successful_subject' => ':application_name 백업 지우기 성공',
    'cleanup_successful_subject_title' => '백업 지우기에 성공했습니다!',
    'cleanup_successful_body' => ':disk_name 디스크에서 :application_name 백업을 성공적으로 지웠습니다.',

    'healthy_backup_found_subject' => ':disk_name 디스크의 :application_name 백업이 정상입니다.',
    'healthy_backup_found_subject_title' => ':application_name에 대한 백업이 정상입니다.',
    'healthy_backup_found_body' => ':application_name에 대한 백업은 정상으로 간주됩니다. 잘 했습니다!',

    'unhealthy_backup_found_subject' => '중요: :application_name에 대한 백업이 비정상입니다.',
    'unhealthy_backup_found_subject_title' => '중요: :application_name에 대한 백업이 비정상입니다. :문제',
    'unhealthy_backup_found_body' => ':disk_name 디스크의 :application_name 백업이 비정상입니다.',
    'unhealthy_backup_found_not_reachable' => '백업 대상에 연결할 수 없습니다. :오류',
    'unhealthy_backup_found_empty' => '이 응용 프로그램의 백업이 전혀 없습니다.',
    'unhealthy_backup_found_old' => ':date에 만들어진 최신 백업은 너무 오래된 것으로 간주됩니다.',
    'unhealthy_backup_found_unknown' => '죄송합니다. 정확한 이유를 알 수 없습니다.',
    'unhealthy_backup_found_full' => '백업이 너무 많은 스토리지를 사용하고 있습니다. 현재 사용량은 :disk_usage이며 허용된 한계인 :disk_limit보다 높습니다.',
];
