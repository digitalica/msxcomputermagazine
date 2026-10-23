        DB    &HFE
        DW    &HC000
        DW    EIND
        DW    &HC000

CHPUT:  EQU   &HA2

BUFFER: EQU   &HD000
COPY:   EQU   &HD400
SIZE:   EQU   &H0304

        ORG   &HC000
        LD    HL,BUFFER
        LD    DE,COPY
        LD    BC,SIZE

LOOP0:  LD    A,(DE)          ;check all bytes and print them if not equal
        CP    (HL)
        CALL  NZ,PRINT
        INC   HL
        INC   DE
        DEC   BC
        LD    A,B
        OR    C
        JP    NZ,LOOP0
        RET

PRINT:  LD    A,H             ;address
        CALL  PRTHEX
        LD    A,L
        CALL  PRTHEX
        CALL  PRTSPC
        LD    A,(HL)          ;byte 1
        CALL  PRTHEX
        CALL  PRTSPC
        LD    A,(DE)          ;byte 2
        CALL  PRTHEX
        PUSH  AF
        PUSH  DE
        PUSH  BC
        PUSH  HL
        LD    A,13            ;return
        CALL  CHPUT
        LD    A,10
        CALL  CHPUT
        POP   HL
        POP   BC
        POP   DE
        POP   AF
        RET

PRTSPC: PUSH  AF              ;print a space
        PUSH  BC
        PUSH  DE
        PUSH  HL
        LD    A," "
        CALL  CHPUT
        POP   HL
        POP   DE
        POP   BC
        POP   AF
        RET

PRTHEX: PUSH  HL              ;print a byte in hexadecimal
        PUSH  BC
        PUSH  DE
        PUSH  AF
        PUSH  AF
        RLCA
        RLCA
        RLCA
        RLCA
        LD    B,2
LOOP1:  AND   &H0F
        PUSH  BC
        ADD   A,"0"
        CP    "0"+10
        JR    C,SKIP0
        ADD   A,"A"-"0"-10
SKIP0:  CALL  CHPUT
        POP   BC
        POP   AF
        DJNZ  LOOP1
        POP   DE
        POP   BC
        POP   HL
        RET
EIND:
