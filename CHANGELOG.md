# ActionLoggerBundle Changelog

## [0.2.0-dev] - 2019-02-14
### Change
- **BC-BREAK** Add keyHash to LogActionRelation used to find related log actions.
- **BC-BREAK** Rename table log to log_action and log_relations to log_action_relation

Use the following SQL-Statements (e.g. in your migrations) to update your database:

    RENAME TABLE log TO log_action
    RENAME TABLE log_relations TO log_action_relation
    UPDATE log_action_relation SET key_hash = SHA2(CONCAT(key_entity, ':', key_id), 256);

- **BC-BREAK** Use `entity_mapping` configuration for log action relations instead of class names
- **BC-BREAK** Moved Action classes into Action namespace
- **BC-BREAK** Renamed LoggableActionEntity to Entity\ActionLoggable
- **BC-BREAK** Renamed ActionLoggable method toLogArray to toActionLogArray
- Change log index name to logAction_action_idx
- Set index for LogActionRelation hash: logActionRelation_keyHash_idx index
- Improve ChangeSet value output

## [0.1.3] - 2018-10-24
### Add
- Add loremipsum/route-generator-bundle as required dependency

### Change
- Use htmlspecialchars instead of strip_tags for flash messages
- Update texts

## [0.1.2] - 2018-10-05
### Add
- Add db index for log.action column

### Fix
- Set log.action column max length to 191

## [0.1.1] - 2018-10-05
### Fix
- Fix mapping configuration definition to support alias array

## [0.1.0] - 2018-10-02
### Added
- Initial version
