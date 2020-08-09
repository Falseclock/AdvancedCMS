<?php
/**
 * PKIStatusInfo
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/CMS-EDITOR
 */

namespace Falseclock\AdvancedCMS\Maps;

use FG\ASN1\Identifier;

abstract class PKIStatusInfo
{
    /**
     * PKIStatusInfo ::= SEQUENCE {
     *      status        PKIStatus,
     *      --XXX dont implement PKIXCMP yet
     *      --    statusString  PKIFreeText     OPTIONAL,
     *      failInfo      PKIFailureInfo  OPTIONAL
     * }
     *
     * PKIStatus ::= INTEGER {
     *      granted                (0),
     *      -- when the PKIStatus contains the value zero a TimeStampToken, as
     *      -- requested, is present.
     *      grantedWithMods        (1),
     *      -- when the PKIStatus contains the value one a TimeStampToken,
     *      -- with modifications, is present.
     *      rejection              (2),
     *      waiting                (3),
     *      revocationWarning      (4),
     *      -- this message contains a warning that a revocation is
     *      -- imminent
     *      revocationNotification (5)
     *      -- notification that a revocation has occurred
     * }
     *
     * PKIFailureInfo ::= BIT STRING {
     *      badAlg               (0),
     *      -- unrecognized or unsupported Algorithm Identifier
     *      badRequest           (2),
     *      -- transaction not permitted or supported
     *      badDataFormat        (5),
     *      -- the data submitted has the wrong format
     *      timeNotAvailable    (14),
     *      -- the TSA's time source is not available
     *      unacceptedPolicy    (15),
     *      -- the requested TSA policy is not supported by the TSA.
     *      unacceptedExtension (16),
     *      -- the requested extension is not supported by the TSA.
     *      addInfoNotAvailable (17),
     *      -- the additional information requested could not be understood
     *      -- or is not available
     *      systemFailure       (25)
     *      -- the request cannot be handled due to system failure
     * }
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'status' => ['type' => Identifier::INTEGER],
            'failInfo' => ['optional' => true, 'type' => Identifier::BITSTRING],
        ]
    ];
}
