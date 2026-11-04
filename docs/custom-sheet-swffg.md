# Star Wars FFG shortcuts in custom sheets

Use `[table=swffg]` to add skill shortcuts to the forum composer. The standalone character sheet displays the source table without roll controls.

```text
[_agility=3]

[table=swffg]
Skill | Characteristic | Rank
Ranged (Light) | [_rangedAgility$=agility] | [_rangedLight=2]
Athletics | 3 | 1
[/table]
```

The first row is a heading. Each subsequent row must contain exactly three cells: skill name, characteristic score, and skill rank. Scores may be literal numbers, editable custom fields, or calculated fields. Existing calculations reference named fields declared earlier in the sheet. Use whole numbers from 0 through 10. Invalid rows, checkbox fields, and failed calculations do not generate shortcuts.

In the forum composer, select the character to see its Skills shortcuts. Click the pool icons to prepare the base roll, or a difficulty number from 1 through 5 to include that many difficulty dice. Proficiency is the lower score, and ability is the difference between the scores. A 3/2 skill therefore produces two proficiency dice and one ability die. A 0/0 skill has no base action but retains difficulty actions.

Edits and calculation updates refresh the shortcuts. Prepared rolls remain editable and use the normal forum submission flow. They are not changed retroactively by later sheet edits.

The table modifier is `swffg`; the existing internal roll type remains `starwarsffg`, with Star Wars symbols. Ordinary `[table=rolls]`, Fate, and `[table=d20]` shortcuts remain separate.

## Local checks

Run `php -d short_open_tag=1 tests/swffg-parser.php` for parser regression checks. To exercise the actual composer and custom-sheet JavaScript without an account or database, serve the repository locally with PHP and visit `/tests/swffg-composer.php`, then select Run checks. This fixture adapts sheet loading, saves, and roll-row requests locally; it does not exercise authenticated forum posting.
