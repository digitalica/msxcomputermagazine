        DB    &HFE            ;Header to make it a .bin-file
        DW    &HC000
        DW    EIND
        DW    &HC000

RDHDR:  EQU   &HE1
READ:   EQU   &HE4
MOTOR:  EQU   &HF3
CHPUT:  EQU   &HA2

SIZ1:   EQU   &H11            ;so it breaks after the name
SIZ2:   EQU   &H0301          ;metal gear save file length
BUF1:   EQU   &HCF00
BUF2:   EQU   &HD000
SIZE1:  EQU   &HCEFC
SIZE2:  EQU   &HCEFE

        ORG   &HC000
        CALL  RDHDR           ;Read first header
        DI
        LD    DE,BUF1
        LD    HL,SIZ1
        LD    (MAXSIZ),HL
        CALL  RDTAP           ;Read file-name
        LD    (SIZE1),HL

;print filename to screen
        LD    HL,BUF1+10      ;offset:some bytes before actual name
        LD    B,6
LOOP2:  LD    A,(HL)
        PUSH  HL
        PUSH  BC
        CALL  CHPUT
        POP   BC
        POP   HL
        INC   HL
        DJNZ  LOOP2
        LD    A,13            ;add a return
        CALL  CHPUT
        LD    A,10
        CALL  CHPUT

        CALL  RDHDR
        DI
        LD    DE,BUF2
        LD    HL,SIZ2
        LD    (MAXSIZ),HL
        CALL  RDTAP
        LD    (SIZE2),HL
        RET

RDTAP:  LD    HL,0            ;actual tape-reading
LOOP0:  PUSH  HL
        PUSH  DE
        CALL  READ
        DI
        POP   DE
        POP   HL
        JR    C,STOP
        LD    (DE),A
        INC   DE
        INC   HL
        LD    BC,(MAXSIZ)
        AND   A
        PUSH  HL
        SBC   HL,BC
        POP   HL
        JP    NZ,LOOP0

STOP:   XOR   A
        PUSH  HL
        CALL  MOTOR
        POP   HL
        RET

EIND:

MAXSIZ: DS    2               ;don't put variables in reserved (saved) memory
