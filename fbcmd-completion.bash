# bash completion for fbcmd

fbcmd_commands=$(fbcmd BC_COMMANDS)

_fbcmd() {

  local cur prev
  cur="${COMP_WORDS[COMP_CWORD]}"
  prev="${COMP_WORDS[COMP_CWORD-1]}"

  COMPREPLY=( $(compgen -W "${fbcmd_commands}" -- $cur) )
  return 0

}

complete -F _fbcmd fbcmd
