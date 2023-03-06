# Advanced Workflow Module

## Overview

This module is heavily based on, and is a modification of the [symbiote/silverstripe-advancedworkflow](https://github.com/symbiote/silverstripe-advancedworkflow) customised for DNA Design's specific needs. The changes we've made aren't likely to be desirable for the original module, so  we've created a new module from it rather than maintaining a fork.

## Installation

`composer require dnadesign/silverstripe-workflow`
```
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/dnadesign/silverstripe-workflow.git"
        }
    ]
}
```

The workflow extension is automatically applied to the `SiteTree` class (if available).

## Documentation
 - [User guide](docs/en/userguide/index.md)
 - [Developer documentation](docs/en/index.md)
