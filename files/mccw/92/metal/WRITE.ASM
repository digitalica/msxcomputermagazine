        DB    &HFE
        DW    &HC000
        DW    EIND
        DW    &HC000

WRHDR:  EQU   &HEA
WRITE:  EQU   &HED
MOTOR:  EQU   &HF3
BUF1:   EQU   &HCF00
BUF2:   EQU   &HD000
SIZE1:  EQU   &HCEFC          ;make sure this is where read has stored them
SIZE2:  EQU   &HCEFE

        ORG   &HC000
        LD    A,1             ;long header
        CALL  WRHDR
        DI
        LD    HL,BUF1
        LD    DE,(SIZE1)
        CALL  WRTAP
        XOR   A               ;short header
        CALL  WRHDR
        DI
        LD    HL,BUF2
        LD    DE,(SIZE2)
        CALL  WRTAP
        XOR   A
        JP    MOTOR           ;This will return to the caller

WRTAP:  PUSH  HL
        PUSH  DE
        LD    A,(HL)
        CALL  WRITE
        DI
        POP   DE
        POP   HL
        INC   HL
        DEC   DE
        LD    A,E
        OR    D
        JP    NZ,WRTAP
        RET

EIND:
