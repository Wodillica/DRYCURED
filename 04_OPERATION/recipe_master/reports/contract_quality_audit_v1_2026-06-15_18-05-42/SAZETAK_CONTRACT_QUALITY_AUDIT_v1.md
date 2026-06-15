# Contract quality audit v1

Ovaj audit provjerava jesu li contract draftovi stvarno upotrebljivi za renderer ili su samo kostur.

- Contract datoteka pregledano: 30
- CONTRACT_SKELETON_ONLY: 30

## Pravilo

Ako većina contracta završi kao `CONTRACT_SKELETON_ONLY` ili `CONTRACT_NEEDS_FIELD_EXTRACTION`, prije renderer adaptera mora se napraviti normalizer v2 koji izvlači stvarne vrijednosti, a ne samo status grupa.
