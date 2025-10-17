# ActionLoggerBundle Changelog

## [0.3.0] - 2025-05-06
### Change
- Support symfony 7

## [0.2.0] - 2020-06-21
### Add
- New `EntityDeleteAction`

### Change
- **BC-BREAK** Change comparison in ChangeSet to strict and allow to use a compare function.
- **BC-BREAK** Always flush in ActionLogger::log method regardless of skipPersisting return value.
- **BC-BREAK** Add keyHash to LogActionRelation used to find related log actions.
- **BC-BREAK** Rename table log to log_action and log_relations to log_action_relation

Use the following SQL-Statements (e.g. in your migrations) to update your database:

    RENAME TABLE log TO log_action
    RENAME TABLE log_relations TO log_action_relation
    ALTER TABLE log_action_relation ADD key_hash VARCHAR(64)
    UPDATE log_action_relation SET key_hash = SHA2(CONCAT(key_entity, ':', key_id), 256);

- **BC-BREAK** Use `entity_mapping` configuration for log action relations instead of class names
- **BC-BREAK** Move Action classes into Action namespace
- **BC-BREAK** Move ActionLoggerInterface into Model namespace
- **BC-BREAK** Move ActionFactory into Factory namespace
- **BC-BREAK** Move ActionLogger into Utils namespace
- **BC-BREAK** Rename LoggableActionEntity to Entity\ActionLoggable
- **BC-BREAK** Rename ActionLoggable method toLogArray to toActionLogArray
- **BC-BREAK** Remove ActionEvent name and use class name instead
- **BC-BREAK** Add getLog to ActionInterface
- Change log index name to logAction_action_idx
- Set index for LogActionRelation hash: logActionRelation_keyHash_idx index
- Improve ChangeSet value output

## [0.1.4] - 2019-04-23
### Add
- Support loremipsum/route-generator-bundle v0.2

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
