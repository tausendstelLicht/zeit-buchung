# 2.0.2
Fixed spelling mistake of an class attribute.

# 2.0.1
Fixed PHP Warning `Warning: substr_count(): Offset not contained in string` if record file is empty and it will be checked for an unstopped record session.

# 2.0.0
The second major release contains a completely refactoring of the commands. All business logic was moved into the new class `RecordFile`. Also new commands were added to show the status of the active record session and to report the complete day. Further information are in the readme file.

New commands:
* `status`
* `report`

# 1.0.0
This is the first stable version of lzukowski/zeit-buchung. You can start and stop a new record session. All record sessions will be stored in daily log files. 

Available commands:
* `start <message>`
* `stop`
