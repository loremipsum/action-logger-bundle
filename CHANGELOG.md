# ActionLoggerBundle Changelog

## [0.2.0-dev] - 2019-02-14
### Add 
- Add logActionRelations_key_idx index

### Change
- **BC** Use `entity_mapping` configuration for log action relations instead of class names
- **BC** Add keyHash to LogActionRelation used to find related log actions.
    Use the following SQL-Statement (e.g. in your migrations) to update all hashes:

      UPDATE log_relations SET key_hash = SHA2(CONCAT(key_entity, ':', key_id), 256);

- Change log index name to logAction_action_idx
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
